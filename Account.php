<?php

class Account
{
  private string $id;
  private string $name;
  private string $password;

  private function __construct($id, $name, $password)
  {
    $this->id = $id;
    $this->name = $name;
    $this->password = $password;
  }

  /**
   * @throws Exception
   */
  public static function register($name, $password): Account
  {
    $database = new Database();

    $is_name_duplicate = $database->query('
      SELECT COUNT(*) AS `count`
      FROM `Accounts`
      WHERE `name` = ?
    ', [$name])->get_result()->fetch_assoc()['count'] > 0;

    if ($is_name_duplicate) {
      throw new Exception("Account name must be unique");
    } else {
      $password = password_hash($password, PASSWORD_ARGON2ID);

      $is_insert_successful = $database->query('
        INSERT INTO `Accounts`
          (`id`, `name`, `password`)
        VALUES (UUID(), ?, ?)
      ', [$name, $password])->affected_rows > 0;

      if ($is_insert_successful) {
        $id = $database->get_connection()->insert_id;
        return new Account($id, $name, $password);
      } else {
        throw new Exception("A database error has occurred. Try again later.");
      }
    }
  }
}