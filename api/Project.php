<?php

readonly class Project
{
  public function __construct(
    public string $id,
    public string $name,
    public string $description,
    public User   $lead,
    public Team   $team,
    public array  $tasks
  ) {}

  public static function router(): void {}

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
    ', [$id, $name, $description, $lead->id, $team->id]
      )->affected_rows == 1;

    return $is_insert_successful ? Project::get($id) : null;
  }

  /**
   * @throws Exception
   */
  public static function get(string $id): Project|null
  {
    $database = new Database();
    $project_result = $database->query('
      SELECT `name`, `description`, `lead_id`, `team_id`
      FROM `projects`
      WHERE `id` = ?
    ', [$id])->get_result();

    return $project_result->num_rows == 1 ? new Project(
      $id,
      $project_result['name'],
      $project_result['description'],
      User::get($project_result['lead_id']),
      Team::get($project_result['team_id']),
      Project::get_tasks($id)
    ) : null;
  }

  /**
   * @throws Exception
   */
  public static function get_tasks(string $project_id): array
  {
    $database = new Database();
    $tasks_result = $database->query('
      SELECT `id`
      FROM `tasks`
      WHERE `project_id` = ?
    ', [$project_id])->get_result();

    $tasks = array();
    while ($task_result = $tasks_result->fetch_assoc()) {
      $tasks[] = Task::get($task_result['id']);
    }

    return $tasks;
  }

  /**
   * @throws Exception
   */
  public function update(
    ?string $name,
    ?string $description
  ): Project
  {
    $database = new Database();
    $is_update_successful = $database->query('
      UPDATE `projects`
        SET `name` = ?,
            `description` = ?
      WHERE `id` = ?
    ', [
        $name ?? $this->name,
        $description ?? $this->description,
        $this->id
      ])->affected_rows == 1;

    return $is_update_successful ? new Project(
      $this->id,
      $name ?? $this->name,
      $description ?? $this->description,
      $this->lead,
      $this->team,
      $this->tasks
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
      FROM `projects`
      WHERE `id` = ?
    ', [$this->id])->affected_rows == 1;
  }
}

Project::router();
