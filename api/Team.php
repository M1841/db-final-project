<?php

readonly class Team
{
  public function __construct(
    public string  $id,
    public string  $name,
    public ?string $description = "",
    public ?array  $members = array(),
    public ?array  $projects = array()
  ) {}

  public static function router(): void {}

  /**
   * @throws Exception
   */
  public static function add(
    string  $name,
    ?string $description = ""
  ): Team|null
  {
    $database = new Database();

    $id = $database->generate_id();

    $is_insert_successful = $database->query('
      INSERT INTO `teams`
      VALUES (?, ?, ?)
    ', [$id, $name, $description])->affected_rows == 1;

    if ($is_insert_successful) {
      return Team::get($id);
    } else {
      return NULL;
    }
  }

  /**
   * @throws Exception
   */
  public static function get(string $id): Team
  {
    $database = new Database();

    $team_result = $database->query('
      SELECT `name`, `description`
      FROM `teams`
      WHERE `id` = ?
    ', [$id])->get_result()->fetch_assoc();

    $members_result = $database->query('
      SELECT `users`.`id`
      FROM `teams`
      JOIN `_member_of_`
        ON `_member_of_`.`team_id` = ?
      JOIN `users`
        ON `_member_of_`.`user_id` = `users`.`id`
    ', [$id])->get_result();

    $members = array();
    while ($member = $members_result->fetch_assoc()) {
      $members[] = User::get($member['id']);
    }

    return new Team(
      $id,
      $team_result['name'],
      $team_result['description'],
      $members,
    );
  }

  /**
   * @throws Exception
   */
  private static function create(string $name, array $member_ids, ?string $description = ""): Team
  {
    $database = new Database();

    $id = $database->generate_id();

    $is_team_insert_successful = $database->query('
      INSERT INTO `teams`
        (`id`, `name`, `description`)
      VALUES (UUID(), ?, ?)
    ', [$name, $description])->affected_rows > 0;

    if ($is_team_insert_successful) {
      foreach ($member_ids as $member_id) {
        if (User::is_registered($member_id)) {
          $member_name = $database->query('
            SELECT `name`
            FROM `users`
            WHERE `id` = ?
          ', [$member_id])->get_result()->fetch_assoc()['name'];
        }
      }
      return new Team($id, $name);
    } else {
      throw new Exception('A database error has occurred. Try again later.');
    }
  }
}
