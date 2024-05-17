<?php

abstract class Database
{
  /**
   * @throws Exception
   */
  public static function generate_id(): string
  {
    return Database::query('SELECT UUID() AS `id`')["result"]->fetch_assoc()['id'];
  }

  /**
   * @throws Exception
   */
  public static function query(string $query_string, ?array $params = NULL):
  array
  {
    $connection = new mysqli('localhost', 'root', '', 'db_final_project', '3306');

    $query = $connection->prepare($query_string);

    if ($query === false) {
      throw new Exception("Couldn't prepare query for execution");
    }
    if ($params) {
      $query->bind_param(
        str_repeat('s', count($params)),
        ...$params
      );
    }
    $query->execute();

    $output = [
      'result' => $query->get_result(),
      'affected_rows' => $query->affected_rows
    ];

    $connection->close();

    return $output;
  }
}
