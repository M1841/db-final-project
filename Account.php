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

      $query_insert_account = $database->query('
        INSERT INTO `Accounts`
          (`id`, `name`, `password`)
        VALUES (UUID(), ?, ?)
      ', [$name, $password]);

      if ($query_insert_account->affected_rows > 0) {
        $id = $database->get_connection()->insert_id;
        return new Account($id, $name, $password);
      } else {
        throw new Exception("A database error has occurred. Try again later.");
      }
    }
  }
}