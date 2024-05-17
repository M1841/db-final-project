<?php
require_once __DIR__ . '/Session.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Team.php';

readonly class User
{

  public function __construct(
    public string $id,
    public string $name,
    public string $password
  ) {}

  public static function router(): void
  {
    define('IS_AUTHENTICATING',
      $_SERVER['REQUEST_METHOD'] === 'POST'
      && isset($_POST['name'])
      && isset($_POST['password'])
      && isset($_POST['auth_type'])
      && (
        $_POST['auth_type'] === 'register'
        || $_POST['auth_type'] === 'login'
      )
    );
    define('IS_LOGGING_OUT',
      $_SERVER['REQUEST_METHOD'] === 'POST'
      && isset($_POST['auth_type'])
      && $_POST['auth_type'] === 'logout'
    );

    switch (true) {
      case IS_AUTHENTICATING:
      {
        $name = $_POST['name'];
        $password = $_POST['password'];
        $auth_type = $_POST['auth_type'];

        try {
          $user = $auth_type === 'register'
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
            'auth_type' => $auth_type
          ]);

          header('Location: ../');
          exit();
        }
        break;
      }
      case IS_LOGGING_OUT:
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
      $is_password_correct = password_verify($password, $user->password)
        || $password == $user->password;

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
  public static function add(string $name, string $password): User|null
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
      SELECT `teams`.`id`
      FROM `_member_of_`
      JOIN `teams`
        ON `_member_of_`.`team_id` = `teams`.`id`
      WHERE `_member_of_`.`user_id` = ?
    ', [$this->id])["result"];

    $teams = array();
    while ($team_result = $teams_result->fetch_assoc()) {
      $teams[] = Team::get($team_result['id']);
    }

    return $teams;
  }

  /**
   * @throws Exception
   */
  public function get_projects(): array
  {
    $projects_result = Database::query('
      SELECT `id`
      FROM `projects`
      WHERE `lead_id` = ?
    ', [$this->id])["result"];

    $projects = array();
    while ($project_result = $projects_result->fetch_assoc()) {
      $projects[] = Project::get($project_result['id']);
    }

    foreach ($this->get_tasks() as $task) {
      $projects[] = $task->project;
    }

    return $projects;
  }

  /**
   * @throws Exception
   */
  public function get_tasks(): array
  {
    $tasks_result = Database::query('
      SELECT `id`
      FROM `tasks`
      WHERE `user_id` = ?
    ', [$this->id])["result"];

    $tasks = array();
    while ($task_result = $tasks_result->fetch_assoc()) {
      $tasks[] = Task::get($task_result['id']);
    }

    return $tasks;
  }

  /**
   * @throws Exception
   */
  public function update(?string $name, ?string $password): User
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
    return Database::query('
      INSERT INTO `_member_of_`
      VALUES (?, ?)
    ', [$this->id, $team->id])['affected_rows'] === 1;
  }
}

User::router();
