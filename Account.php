<?php
require_once 'Database.php';
require_once 'Session.php';

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

  /**
   * @throws Exception
   */
  public static function register(string $name, string $password): Account
  {
    $database = new Database();

    $is_name_unique = $database->query('
      SELECT COUNT(`id`) AS `count`
      FROM `Accounts`
      WHERE `name` = ?
    ', [$name])->get_result()->fetch_assoc()['count'] === 0;

    if ($is_name_unique) {
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
  public static function login(string $name, string $password): Account
  {
    $database = new Database();

    $is_user_registered = $database->query('
      SELECT COUNT(*) AS `count`
      FROM `Accounts`
      WHERE `name` = ?
    ', [$name])->get_result()->fetch_assoc()['count'] === 1;

    if ($is_user_registered) {
      $password = password_hash($password, PASSWORD_ARGON2ID);

      $is_password_correct = $database->query('
        SELECT COUNT(`id`) as `count`
        FROM `Accounts`
        WHERE `name` = ?
          AND `password` = ?
      ', [$name, $password])->get_result()->fetch_assoc()['count'] === 1;

      if ($is_password_correct) {
        $id = $database->query('
          SELECT `id`
          FROM `Accounts`
          WHERE `name` = ?
        ', [$name])->get_result()->fetch_assoc()['id'];

        return new Account($id, $name, $password);
      } else {
        throw new Exception('Incorrect password');
      }
    } else {
      throw new Exception('Account ' . $name . ' does not exist');
    }
  }

  public function get_id(): string
  {
    return $this->id;
  }

  public function get_name(): string
  {
    return $this->name;
  }
}

define('IS_AUTHENTICATING',
  $_SERVER['REQUEST_METHOD'] === 'POST'
  && isset($_POST['name'])
  && isset($_POST['password'])
  && isset($_POST['auth_type'])
);

switch (true) {
  case IS_AUTHENTICATING:
  {
    $name = $_POST['name'];
    $password = $_POST['password'];
    $auth_type = $_POST['auth_type'];

    try {
      if ($auth_type !== 'register' && $auth_type !== 'login') {
        throw new Exception('Invalid authentication type. Try again later.');
      }

      $account = $auth_type === 'register'
        ? Account::register($name, $password)
        : Account::login($name, $password);

      Session::set('account', [
        'id' => $account->get_id(),
        'name' => $account->get_name()
      ]);

      Session::unset('error');
    } catch (Exception $err) {
      Session::unset();

      Session::set('error', '
      <p style="color: #d93030">' . $err->getMessage() . '</p>
    ');
    } finally {
      header('Location: ./');
      die();
    }
  }
  default:
  {
    header('Location: ./');
    die();
  }
}