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
    $id = Database::generate_id();

    $is_insert_successful = Database::query('
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
      ])["affected_rows"] == 1;

    return $is_insert_successful ? Task::get($id) : null;
  }

  /**
   * @throws Exception
   */
  public static function get(string $id): Task|null
  {
    $task_result = Database::query('
      SELECT `name`, `description`, `status`, `priority`, `user_id`, `project_id`
      FROM `tasks`
      WHERE `id` = ?
    ', [$id])["result"];

    if ($task_result->num_rows == 1) {
      $task = $task_result->fetch_assoc();

      return new Task(
        $id,
        $task['name'],
        $task['description'],
        Project::get($task['project_id']),
        TaskStatus::tryFrom($task['status']),
        TaskPriority::tryFrom($task['priority']),
        User::get($task['user_id'])
      );
    } else {
      return null;
    }
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
    $is_update_successful = Database::query('
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
      ])["affected_rows"] == 1;

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
    return Database::query('
      DELETE 
      FROM `tasks`
      WHERE `id` = ?
    ', [$this->id])["affected_rows"] == 1;
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
