<?php

readonly class Project
{
  public function __construct(
    public string $id,
    public string $name,
    public string $description,
    public User   $lead,
    public Team   $team,
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
    $id = Database::generate_id();

    $is_insert_successful = Database::query('
      INSERT INTO `projects`
      VALUES (?, ?, ?, ?, ?)
    ', [$id, $name, $description, $lead->id, $team->id]
      )["affected_rows"] == 1;

    return $is_insert_successful ? Project::get($id) : null;
  }

  /**
   * @throws Exception
   */
  public static function get(string $id): Project|null
  {
    $project_result = Database::query('
      SELECT `name`, `description`, `lead_id`, `team_id`
      FROM `projects`
      WHERE `id` = ?
    ', [$id])["result"];

    if ($project_result->num_rows == 1) {
      $project = $project_result->fetch_assoc();

      return new Project(
        $id,
        $project['name'],
        $project['description'],
        User::get($project['lead_id']),
        Team::get($project['team_id'])
      );
    } else {
      return null;
    }
  }

  /**
   * @throws Exception
   */
  public function get_tasks(): array
  {
    $tasks_result = Database::query('
      SELECT `id`
      FROM `tasks`
      WHERE `project_id` = ?
    ', [$this->id])["result"];

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
    $is_update_successful = Database::query('
      UPDATE `projects`
        SET `name` = ?,
            `description` = ?
      WHERE `id` = ?
    ', [
        $name ?? $this->name,
        $description ?? $this->description,
        $this->id
      ])["affected_rows"] == 1;

    return $is_update_successful ? new Project(
      $this->id,
      $name ?? $this->name,
      $description ?? $this->description,
      $this->lead,
      $this->team
    ) : $this;
  }

  /**
   * @throws Exception
   */
  public function remove(): bool
  {
    return Database::query('
      DELETE
      FROM `projects`
      WHERE `id` = ?
    ', [$this->id])["affected_rows"] == 1;
  }
}

Project::router();
