<?php
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Team.php';
require_once __DIR__ . '/Project.php';
require_once __DIR__ . '/Task.php';
require_once __DIR__ . '/Request.php';
require_once __DIR__ . '/Session.php';

readonly class User
{

  public function __construct(
    public string $id,
    public string $name,
    public string $password
  ) {}

  public static function router(): void
  {
    $IS_AUTHENTICATING = Request::method() === 'POST'
      && Request::post('name')
      && Request::post('password')
      && (
        Request::post('action') === 'register'
        || Request::post('action') === 'login'
      );

    $IS_LOGGING_OUT = Request::method() === 'POST'
      && Request::post('action') === 'logout';

    switch (true) {
      case $IS_AUTHENTICATING:
      {
        $name = Request::post('name');
        $password = Request::post('password');
        $action = Request::post('action');

        try {
          $user = $action === 'register'
            ? User::register($name, $password)
            : User::login($name, $password);

          Session::set('user', $user);

          Session::unset('error');
        } catch (Exception $err) {
          Session::unset();

          Session::set('error', $err->getMessage());
        } finally {
          Session::set('auth_form', [
            'name' => $name,
            'password' => $password,
            'action' => $action
          ]);

          header('Location: ../');
          exit();
        }
        break;
      }
      case $IS_LOGGING_OUT:
      {
        Session::unset('user');
        header('Location: ../');
        exit();
        break;
      }
    }
  }

  /**
   * @throws Exception
   */
  private static function register(string $name, string $password): User
  {
    $is_name_taken = User::get($name) !== null;

    if ($is_name_taken) {
      throw new Exception('User name is taken');
    } else {
      $user = User::add($name, $password);

      if ($user !== null) {
        return $user;
      } else {
        throw new Exception('A database error has occurred. Try again later.');
      }
    }
  }

  /**
   * @throws Exception
   */
  private static function login(string $name, string $password): User
  {
    $user = User::get($name);
    $is_registered = $user !== null;

    if ($is_registered) {
      $is_password_correct = password_verify($password, $user->password);

      if ($is_password_correct) {
        return $user;
      } else {
        throw new Exception('Incorrect password');
      }
    } else {
      throw new Exception('User does not exist');
    }
  }

  /**
   * @throws Exception
   */
  private static function add(string $name, string $password): User|null
  {
    $id = Database::generate_id();
    $password = password_hash($password, PASSWORD_BCRYPT);

    $is_insert_successful = Database::query('
      INSERT INTO `users`
      VALUES (?, ?, ?)
    ', [$id, $name, $password])["affected_rows"] == 1;

    return $is_insert_successful ? User::get($id) : null;
  }

  /**
   * @throws Exception
   */
  public static function get(string $identifier): User|null
  {
    $user_result = Database::query('
      SELECT `id`, `name`, `password`
      FROM `users`
      WHERE `id` = ?
        OR `name` = ?
    ', [$identifier, $identifier])["result"];

    if ($user_result->num_rows == 1) {
      $user = $user_result->fetch_assoc();

      return new User(
        $user['id'],
        $user['name'],
        $user['password']
      );
    } else {
      return null;
    }
  }

  /**
   * @throws Exception
   */
  public function get_teams(): array
  {
    $teams_result = Database::query('
      SELECT `teams`.`id`, `teams`.`name`, `teams`.`description`
      FROM `_member_of_`
      JOIN `teams`
        ON `_member_of_`.`team_id` = `teams`.`id`
      WHERE `_member_of_`.`user_id` = ?
      ORDER BY `teams`.`name`, `teams`.`description`, `teams`.`id`
    ', [$this->id])["result"];

    $teams = [];
    while ($team_result = $teams_result->fetch_assoc()) {
      $teams[] = new Team(
        $team_result["id"],
        $team_result["name"],
        $team_result["description"]
      );
    }

    return $teams;
  }

  /**
   * @throws Exception
   */
  public function get_projects(): array
  {
    $projects_result = Database::query('
      SELECT `id`, `name`, `description`, `team_id`
      FROM `projects`
      WHERE `lead_id` = ?
      ORDER BY `team_id`, `name`, `description`, `id`
    ', [$this->id])["result"];

    $projects = [];
    while ($project_result = $projects_result->fetch_assoc()) {
      $projects[] = new Project(
        $project_result["id"],
        $project_result["name"],
        $project_result["description"],
        $this,
        Team::get($project_result["team_id"])
      );
    }

    foreach ($this->get_tasks() as $task) {
      if (!in_array($task->project, $projects)) {
        $projects[] = $task->project;
      }
    }

    return $projects;
  }

  /**
   * @throws Exception
   */
  public function get_tasks(): array
  {
    $tasks_result = Database::query('
      SELECT `id`, `name`, `description`, `status`, `priority`, `project_id`
      FROM `tasks`
      WHERE `user_id` = ?
      ORDER BY `project_id`, `priority`, `status`, `name`, `description`, `id`
    ', [$this->id])["result"];

    $tasks = [];
    while ($task_result = $tasks_result->fetch_assoc()) {
      $tasks[] = new Task(
        $task_result["id"],
        $task_result["name"],
        $task_result["description"],
        Project::get($task_result["project_id"]),
        TaskStatus::tryFrom($task_result["status"]),
        TaskPriority::tryFrom($task_result["priority"]),
        $this
      );
    }

    return $tasks;
  }

  /**
   * @throws Exception
   */
  private function update(?string $name, ?string $password): User
  {
    if ($name !== null) {
      $user_with_name = User::get($name);
      $is_name_taken = $user_with_name !== null && $user_with_name->id != $this->id;

      if ($is_name_taken) {
        throw new Exception('User name is taken');
      }
    }
    $is_update_successful = Database::query('
        UPDATE `users`
          SET `name` = ?,
            `password` = ?
        WHERE `id` = ?
      ', [
        $name ?? $this->name,
        $password ?? $this->password,
        $this->id
      ])["affected_rows"] == 1;

    return $is_update_successful ? new User(
      $this->id,
      $name ?? $this->name,
      $password ? password_hash($password, PASSWORD_BCRYPT) : $this->password
    ) : $this;
  }

  /**
   * @throws Exception
   */
  public function join(Team $team): bool
  {
    if (!$this->is_in_team($team)) {
      return Database::query('
      INSERT INTO `_member_of_`
      VALUES (?, ?)
    ', [$this->id, $team->id])['affected_rows'] === 1;
    } else {
      throw new Exception('You are already in that team');
    }
  }

  /**
   * @throws Exception
   */
  public function is_in_team(Team $team): bool
  {
    return Database::query('
      SELECT COUNT(*) AS `count`
      FROM `_member_of_`
      WHERE `user_id` = ?
        AND `team_id` = ?
    ', [$this->id, $team->id])['result']->fetch_assoc()['count'] > 0;
  }
}

User::router();
