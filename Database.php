<?php

class Database
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

  public function get_connection(): mysqli
  {
    return $this->connection;
  }

  /**
   * @throws Exception
   */
  public function query(string $query_string, array $params): mysqli_stmt
  {
    $query = $this->connection->prepare($query_string);

    if ($query === false) {
      throw new Exception("Couldn't prepare query for execution");
    }

    $query->bind_param(
      str_repeat('s', count($params)),
      ...$params
    );
    $query->execute();

    return $query;
  }
}