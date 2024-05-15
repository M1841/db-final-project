<?php

readonly class Project
{
  public function __construct(
    public string $id,
    public string $name,
    public string $description,
    public User   $lead,
    public Team   $team,
    public ?array $tasks = array()
  ) {}

  /**
   * @throws Exception
   */
  public static function add(
    string $name,
    string $description,
    User   $lead,
    Team   $team
  ): Project|null
  {
    $database = new Database();

    $id = $database->generate_id();

    $is_insert_successful = $database->query('
      INSERT INTO `projects`
      VALUES (?, ?, ?, ?, ?)
    ', [$id, $name, $description, $lead->id, $team->id])->affected_rows == 1;

    if ($is_insert_successful) {
      return Project::get($id);
    } else {
      return NULL;
    }
  }

  /**
   * @throws Exception
   */
  public static function get(string $id): Project
  {
    $database = new Database();

    $project_result = $database->query('
      SELECT `name`, `description`, `lead_id`, `team_id`
      FROM `projects`
      WHERE `id` = ?
    ', [$id])->get_result()->fetch_assoc();

    $tasks_result = $database->query('
      SELECT `id`
      FROM `tasks`
      WHERE `project_id` = ?
    ', [$id])->get_result();

    $tasks = array();
    while ($task = $tasks_result->fetch_assoc()) {
      $tasks[] = Task::get($task['id']);
    }

    return new Project(
      $id,
      $project_result['name'],
      $project_result['description'],
      User::get($project_result['lead_id']),
      Team::get($project_result['team_id']),
      $tasks
    );
  }

  /**
   * @throws Exception
   */
  public function update(): bool
  {
    $database = new Database();

    return $database->query('
      UPDATE `projects`
        SET `name` = ?,
            `description` = ?,
            `lead_id` = ?,
            `team_id` = ?
      WHERE `id` = ?
    ', [
        $this->name,
        $this->description,
        $this->lead->id,
        $this->team->id
      ])->affected_rows == 1;
  }

  /**
   * @throws Exception
   */
  public function remove(): bool
  {
    $database = new Database();

    return $database->query('
      DELETE
      FROM `projects`
      WHERE `id` = ?
    ', [$this->id])->affected_rows == 1;
  }
}
