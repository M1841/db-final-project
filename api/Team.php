<?php

readonly class Team
{
  public function __construct(
    public string $id,
    public string $name
  ) {}

  public static function controller(): void {}

  /**
   * @throws Exception
   */
  private static function create(string $name, array $member_ids): Team
  {
    $database = new Database();

    $id = $database->generate_id();

    $is_team_insert_successful = $database->query('
      INSERT INTO `teams`
        (`id`, `name`)
      VALUES (UUID(), ?)
    ', [$name])->affected_rows > 0;

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
