<?php

readonly class Team
{
  public function __construct(
    public string $id,
    public string $name,
    public string $description,
    public array  $members,
    public array  $projects
  ) {}

  public static function router(): void {}

  /**
   * @throws Exception
   */
  public static function add(
    string $name,
    string $description
  ): Team|null
  {
    $database = new Database();

    $id = $database->generate_id();

    $is_insert_successful = $database->query('
      INSERT INTO `teams`
      VALUES (?, ?, ?)
    ', [$id, $name, $description])->affected_rows == 1;

    return $is_insert_successful ? Team::get($id) : null;
  }

  /**
   * @throws Exception
   */
  public static function get(string $id): Team|null
  {
    $database = new Database();
    $team_result = $database->query('
      SELECT `name`, `description`
      FROM `teams`
      WHERE `id` = ?
    ', [$id])->get_result();

    return $team_result->num_rows == 1 ? new Team(
      $id,
      $team_result['name'],
      $team_result['description'],
      Team::get_members($id),
      Team::get_projects($id)
    ) : null;
  }

  /**
   * @throws Exception
   */
  public static function get_members(string $team_id): array
  {
    $database = new Database();
    $members_result = $database->query('
      SELECT `users`.`id`
      FROM `_member_of_`
      JOIN `users`
        ON `_member_of_`.`user_id` = `users`.`id`
      WHERE `_member_of_`.`team_id` = ?
    ', [$team_id])->get_result();

    $members = array();
    while ($member_result = $members_result->fetch_assoc()) {
      $members[] = User::get($member_result['id']);
    }

    return $members;
  }

  /**
   * @throws Exception
   */
  public static function get_projects(string $team_id): array
  {
    $database = new Database();
    $projects_result = $database->query('
      SELECT `id`
      FROM `projects`
      WHERE `team_id` = ?
    ', [$team_id])->get_result();

    $projects = array();
    while ($project_result = $projects_result->fetch_assoc()) {
      $projects[] = Project::get($project_result['id']);
    }

    return $projects;
  }

  /**
   * @throws Exception
   */
  public function update(
    ?string $name,
    ?string $description
  ): Team
  {
    $database = new Database();
    $is_update_successful = $database->query('
      UPDATE `teams`
        SET `name` = ?,
            `description` = ?
      WHERE `id` = ?
    ', [
        $name ?? $this->name,
        $description ?? $this->description,
        $this->id
      ])->affected_rows == 1;

    return $is_update_successful ? new Team(
      $this->id,
      $name ?? $this->name,
      $description ?? $this->description,
      $this->members,
      $this->projects
    ) : $this;
  }

  /**
   * @throws Exception
   */
  public function remove(): bool
  {
    $database = new Database();
    return $database->query('
      DELETE
      FROM `teams`
      WHERE `id` = ?
    ', [$this->id])->affected_rows == 1;
  }
}

Team::router();
