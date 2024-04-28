<?php
require_once __DIR__ . '/Session.php';
require_once __DIR__ . '/Database.php';

readonly class User
{
  public function __construct(
    public string $id,
    public string $name
  ) {}

  public static function controller(): void
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

          Session::set('error', '
            <p style="color: #d93030">' . $err->getMessage() . '</p>
          ');
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
    $database = new Database();

    if (!User::is_registered($name)) {
      $password = password_hash($password, PASSWORD_ARGON2ID);

      $id = $database->generate_id();

      $is_insert_successful = $database->query('
        INSERT INTO `users` 
          (`id`, `name`, `password`)
        VALUES (?, ?, ?)
      ', [$id, $name, $password])->affected_rows > 0;

      if ($is_insert_successful) {
        return new User($id, $name);
      } else {
        throw new Exception('A database error has occurred. Try again later.');
      }
    } else {
      throw new Exception('User name is taken');
    }
  }

  /**
   * @throws Exception
   */
  public static function is_registered(string $identifier): bool
  {
    $database = new Database();
    return $database->query('
      SELECT COUNT(`id`) AS `count`
      FROM `users`
      WHERE `name` = ?
        OR `id` = ?
    ', [$identifier, $identifier])->get_result()->fetch_assoc()['count'] > 0;
  }

  /**
   * @throws Exception
   */
  private static function login(string $name, string $password): User
  {
    $database = new Database();

    if (User::is_registered($name)) {

      $aux_query = $database->query('
        SELECT `id`, `password`
        FROM `users`
        WHERE `name` = ?
      ', [$name])->get_result()->fetch_assoc();

      $id = $aux_query['id'];
      $correct_password = $aux_query['password'];

      $is_password_correct = password_verify($password, $correct_password);

      if ($is_password_correct) {
        return new User($id, $name);
      } else {
        throw new Exception('Incorrect password');
      }
    } else {
      throw new Exception('User does not exist');
    }
  }

}

User::controller();