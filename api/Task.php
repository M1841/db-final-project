<?php

readonly class Task
{
  public function __construct(
    public string       $id,
    public string       $name,
    public string       $description,
    public Project      $project,
    public TaskStatus   $status,
    public TaskPriority $priority,
    public User         $user
  ) {}

  public static function router(): void {}

  /**
   * @throws Exception
   */
  public static function add(
    string        $name,
    string        $description,
    Project       $project,
    ?TaskStatus   $status = TaskStatus::NotStarted,
    ?TaskPriority $priority = TaskPriority::Low,
    ?User         $user = null
  ): Task|null
  {
    $database = new Database();

    $id = $database->generate_id();

    $is_insert_successful = $database->query('
      INSERT INTO `tasks`
      VALUES (?, ?, ?, ?, ?, ?, ?)
    ', [
        $id,
        $name,
        $description,
        $project->id,
        $status->value,
        $priority->value,
        $user->id
      ])->affected_rows == 1;

    return $is_insert_successful ? Task::get($id) : null;
  }

  /**
   * @throws Exception
   */
  public static function get(string $id): Task|null
  {
    $database = new Database();

    $task_result = $database->query('
      SELECT `name`, `description`, `status`, `priority`, `user_id`, `project_id`
      FROM `tasks`
      WHERE `id` = ?
    ', [$id])->get_result();

    return $task_result->num_rows == 1 ? new Task(
      $id,
      $task_result['name'],
      $task_result['description'],
      Project::get($task_result['project_id']),
      TaskStatus::tryFrom($task_result['status']),
      TaskPriority::tryFrom($task_result['priority']),
      User::get($task_result['user_id'])
    ) : null;
  }

  /**
   * @throws Exception
   */
  public function update(
    ?string       $name,
    ?string       $description,
    ?TaskStatus   $status,
    ?TaskPriority $priority,
    ?User         $user
  ): Task
  {
    $database = new Database();

    $is_update_successful = $database->query('
      UPDATE `tasks`
      SET `name` = ?,
          `description` = ?,
          `status` = ?,
          `priority` = ?,
          `user_id` = ?
        WHERE `id` = ?
    ', [
        $name ?? $this->name,
        $description ?? $this->description,
        $status ? $status->value : $this->status->value,
        $priority ? $priority->value : $this->priority->value,
        $user ? $user->id : $this->user->id,
        $this->id
      ])->affected_rows == 1;

    return $is_update_successful ? new Task(
      $this->id,
      $name ?? $this->name,
      $description ?? $this->description,
      $this->project,
      $status ?? $this->status,
      $priority ?? $this->priority,
      $user ?? $this->user
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
      FROM `tasks`
      WHERE `id` = ?
    ', [$this->id])->affected_rows == 1;
  }
}

enum TaskStatus: string
{
  case NotStarted = "Not Started";
  case InProgress = "In Progress";
  case Completed = "Completed";
}

enum TaskPriority: string
{
  case Low = "Low";
  case High = "High";
}

Task::router();
