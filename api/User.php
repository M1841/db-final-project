<?php
require_once __DIR__ . '/Session.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/Team.php';

readonly class User
{
  public function __construct(
    public string $id,
    public string $name,
    public string $password,
    public array  $teams,
    public array  $projects,
    public array  $tasks
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
      default:
      {
//        header('Location: ../');
//        exit();
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
    $database = new Database();
    $id = $database->generate_id();
    $password = password_hash($password, PASSWORD_BCRYPT);

    $is_insert_successful = $database->query('
      INSERT INTO `users`
      VALUES (?, ?, ?)
    ', [$id, $name, $password])->affected_rows == 1;

    return $is_insert_successful ? User::get($id) : null;
  }

  /**
   * @throws Exception
   */
  public static function get(string $identifier): User|null
  {
    $database = new Database();
    $user_result = $database->query('
      SELECT `id`, `name`
      FROM `users`
      WHERE `id` = ?
        OR `name` = ?
    ', [$identifier, $identifier])->get_result();

    $teams = User::get_teams($user_result['id']);
    $projects = User::get_projects($user_result['id']);
    $tasks = User::get_tasks($user_result['id']);

    return $user_result->num_rows == 1 ? new User(
      $user_result['id'],
      $user_result['name'],
      $user_result['password'],
      $teams,
      $projects,
      $tasks
    ) : null;
  }

  /**
   * @throws Exception
   */
  public static function get_teams(string $user_id): array
  {
    $database = new Database();
    $teams_result = $database->query('
      SELECT `teams`.`id`
      FROM `_member_of_`
      JOIN `teams`
        ON `_member_of_`.`team_id` = `teams`.`id`
      WHERE `_member_of_`.`user_id` = ?
    ', [$user_id])->get_result();

    $teams = array();
    while ($team_result = $teams_result->fetch_assoc()) {
      $teams[] = Team::get($teams_result['id']);
    }

    return $teams;
  }

  /**
   * @throws Exception
   */
  public static function get_projects(string $user_id): array
  {
    $database = new Database();
    $projects_result = $database->query('
      SELECT `id`
      FROM `projects`
      WHERE `lead_id` = ?
    ', [$user_id])->get_result();

    $projects = array();
    while ($project_result = $projects_result->fetch_assoc()) {
      $projects[] = Project::get($project_result['id']);
    }

    foreach (User::get_tasks($user_id) as $task) {
      $projects[] = $task->project;
    }

    return $projects;
  }

  /**
   * @throws Exception
   */
  public static function get_tasks(string $user_id): array
  {
    $database = new Database();
    $tasks_result = $database->query('
      SELECT `id`
      FROM `tasks`
      WHERE `user_id` = ?
    ', [$user_id])->get_result();

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
    $database = new Database();

    if ($name !== null) {
      $user_with_name = User::get($name);
      $is_name_taken = $user_with_name !== null && $user_with_name->id != $this->id;

      if ($is_name_taken) {
        throw new Exception('User name is taken');
      }
    }
    $is_update_successful = $database->query('
        UPDATE `users`
          SET `name` = ?,
            `password` = ?
        WHERE `id` = ?
      ', [
        $name ?? $this->name,
        $password ?? $this->password,
        $this->id
      ])->affected_rows == 1;

    return $is_update_successful ? new User(
      $this->id,
      $name ?? $this->name,
      $password ? password_hash($password, PASSWORD_BCRYPT) : $this->password,
      $this->teams,
      $this->projects,
      $this->tasks
    ) : $this;
  }
}

User::router();
