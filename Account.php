<?php
require_once 'Session.php';
require_once 'Database.php';

class Account
{
  private string $id;
  private string $name;
  private string $password;

  private function __construct(string $id, string $name, string $password)
  {
    $this->id = $id;
    $this->name = $name;
    $this->password = $password;
  }

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
          $account = $auth_type === 'register'
            ? Account::register($name, $password)
            : Account::login($name, $password);

          Session::set('account', [
            'id' => $account->id,
            'name' => $account->name
          ]);

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

          header('Location: ./');
          exit();
        }
      }
      case IS_LOGGING_OUT:
      {
        Session::unset('account');
        header('Location: ./');
        exit();
      }
      default:
      {
        header('Location: ./');
        exit();
      }
    }
  }

  /**
   * @throws Exception
   */
  public static function register(string $name, string $password): Account
  {
    $database = new Database();

    if (!Account::is_registered($name)) {
      $password = password_hash($password, PASSWORD_ARGON2ID);

      $is_insert_successful = $database->query('
        INSERT INTO `Accounts` 
          (`id`, `name`, `password`)
        VALUES (UUID(), ?, ?)
      ', [$name, $password])->affected_rows > 0;

      if ($is_insert_successful) {
        $id = $database->query('
          SELECT `id`
          FROM `Accounts`
          WHERE `name` = ?
        ', [$name])->get_result()->fetch_assoc()['id'];

        return new Account($id, $name, $password);
      } else {
        throw new Exception('A database error has occurred. Try again later.');
      }
    } else {
      throw new Exception('Account name is taken');
    }
  }

  /**
   * @throws Exception
   */
  public static function is_registered(string $name): bool
  {
    $database = new Database();
    return $database->query('
      SELECT COUNT(`id`) as `count`
      FROM `Accounts`
      WHERE `name` = ?
    ', [$name])->get_result()->fetch_assoc()['count'] > 0;
  }

  /**
   * @throws Exception
   */
  public static function login(string $name, string $password): Account
  {
    $database = new Database();

    if (Account::is_registered($name)) {
      $aux_query = $database->query('
        SELECT `id`, `password`
        FROM `Accounts`
        WHERE `name` = ?
      ', [$name])->get_result()->fetch_assoc();

      $correct_password = $aux_query['password'];
      $is_password_correct = password_verify($password, $correct_password);

      if ($is_password_correct) {
        $id = $aux_query['id'];
        return new Account($id, $name, $password);
      } else {
        throw new Exception('Incorrect password');
      }
    } else {
      throw new Exception('Account does not exist');
    }
  }

  public function __get(string $property): mixed
  {
    if ($property !== 'password' && property_exists($this, $property)) {
      return $this->$property;
    }
    return NULL;
  }
}

Account::controller();