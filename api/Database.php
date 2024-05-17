<?php

readonly class Database
{
  private mysqli $connection;

  public function __construct(
    ?string $host = 'localhost',
    ?string $user = 'root',
    ?string $password = '',
    ?string $schema = 'db_final_project',
    ?string $port = '3306'
  )
  {
    $this->connection = new mysqli($host, $user, $password, $schema, $port);
  }

  public function __destruct()
  {
    $this->connection->close();
  }

  /**
   * @throws Exception
   */
  public function generate_id(): string
  {
    return $this
      ->query('SELECT UUID() AS `id`')
      ->get_result()
      ->fetch_assoc()['id'];
  }

  /**
   * @throws Exception
   */
  public function query(string $query_string, ?array $params = NULL): mysqli_stmt
  {
    $query = $this->connection->prepare($query_string);

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

    return $query;
  }
}
