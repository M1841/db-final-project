<?php

readonly class Task
{
  public function __construct(
    public string  $id,
    public string  $name,
    public string  $description,
    public Project $project,
    public ?string $status = "",
    public ?string $priority = "Low",
    public ?User   $user = NULL
  ) {}

  /**
   * @throws Exception
   */
  public static function add(
    string  $name,
    string  $description,
    Project $project,
    ?string $status = "",
    ?string $priority = "Low",
    ?User   $user = NULL
  ): Task|null
  {
    $database = new Database();

    $id = $database->generate_id();

    $is_insert_successful = $database->query('
      INSERT INTO `tasks`
      VALUES (?, ?, ?, ?, ?, ?, ?)
    ', [$id, $name, $description, $project->id, $status, $priority, $user->id])
        ->affected_rows == 1;

    if ($is_insert_successful) {
      return Task::get($id);
    } else {
      return NULL;
    }
  }

  /**
   * @throws Exception
   */
  public static function get(string $id): Task
  {
    $database = new Database();

    $task_result = $database->query('
      SELECT `name`, `description`, `status`, `priority`, `user_id`, `project_id`
      FROM `tasks`
      WHERE `id` = ?
    ', [$id])->get_result()->fetch_assoc();

    return new Task(
      $id,
      $task_result['name'],
      $task_result['description'],
      Project::get($task_result['project_id']),
      $task_result['status'],
      $task_result['priority'],
      User::get($task_result['user_id'])
    );
  }

  /**
   * @throws Exception
   */
  public function update(): bool
  {
    $database = new Database();

    return $database->query('
      UPDATE `tasks`
      SET `name` = ?,
          `description` = ?,
          `status` = ?,
          `priority` = ?,
          `user_id` = ?
        WHERE `id` = ?
    ', [
        $this->name,
        $this->description,
        $this->status,
        $this->priority,
        $this->user->id
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
      FROM `tasks`
      WHERE `id` = ?
    ', [$this->id])->affected_rows == 1;
  }
}
