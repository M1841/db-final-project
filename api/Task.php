<?php
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Team.php';
require_once __DIR__ . '/Project.php';
require_once __DIR__ . '/Request.php';
require_once __DIR__ . '/Session.php';

readonly class Task
{
  public function __construct(
    public string       $id,
    public string       $name,
    public string       $description,
    public Project      $project,
    public TaskStatus   $status,
    public TaskPriority $priority,
    public ?User        $user = null
  ) {}

  public static function router(): void
  {
    $IS_CREATING = Request::method() === 'POST'
      && Session::get('user') !== null
      && Request::post('project_id')
      && Request::post('name')
      && Request::post('action') === 'create'
      && Request::post('resource') === 'task'
      && Request::post('user_id')
      && Request::post('priority')
      && Request::post('status');

    $IS_EDITING = Request::method() === 'POST'
      && Session::get('user') !== null
      && Request::post('id')
      && Request::post('name')
      && Request::post('action') === 'edit'
      && Request::post('resource') === 'task'
      && Request::post('user_id')
      && Request::post('priority')
      && Request::post('status');

    $IS_DELETING = Request::method() === 'POST'
      && Session::get('user') !== null
      && Request::post('id')
      && Request::post('action') === 'delete'
      && Request::post('resource') === 'task';

    switch (true) {
      case $IS_CREATING:
      {
        $project_id = Request::post('project_id');
        $name = Request::post('name');
        $description = Request::post('description');
        $user = Session::get('user');
        $user_id = Request::post('user_id');
        $priority = TaskPriority::tryFrom(Request::post('priority')) ?? TaskPriority::Low;
        $status = TaskStatus::tryFrom(Request::post('status')) ??
          TaskStatus::NotStarted;

        try {
          $project = Project::get($project_id);
          if ($project !== null) {
            if ($user->id === $project->lead->id) {
              if ($user_id !== 'null') {
                $user = User::get($user_id);
                if ($user !== null && $user->is_in_team($project->team)) {
                  $task = Task::add($name, $description, $project, $user,
                    $status, $priority);
                } else {
                  throw new Exception('You cannot assign a task to a user outside the team');
                }
              } else {
                $task = Task::add($name, $description, $project);
              }
              Session::unset('error');

              header('Location: ../task?id=' . $task->id);
              exit();
            } else {
              throw new Exception('You cannot add a task to a project you are not the lead of');
            }
          } else {
            throw new Exception('Could not find project');
          }
        } catch (Exception $err) {
          Session::set('error', $err->getMessage());
        }

        header('Location: ../tasks');
        exit();
        break;
      }
      case $IS_EDITING:
      {
        $id = Request::post('id');
        $name = Request::post('name');
        $description = Request::post('description');
        $user = Session::get('user');
        $user_id = Request::post('user_id');
        $priority = TaskPriority::tryFrom(Request::post('priority'));
        $status = TaskStatus::tryFrom(Request::post('status'));

        try {
          $task = Task::get($id);
          if ($task !== null) {
            $project = $task->project;
            if ($user->is_in_team($project->team)) {
              if ($user->id === $project->lead->id || $user->id === $task->user->id) {
                if ($user_id !== 'null') {
                  $user = User::get($user_id);
                  if ($user !== null && $user->is_in_team($project->team)) {
                    $task->update(
                      $name,
                      $description,
                      $user,
                      $status ?? $task->status,
                      $priority ?? $task->priority
                    );
                  } else {
                    throw new Exception('You cannot assign a task to a user outside the team');
                  }
                } else {
                  $task->update(
                    $name,
                    $description,
                    null,
                    $status ?? $task->status,
                    $priority ?? $task->priority
                  );
                }
                Session::unset('error');

                header('Location: ../task?id=' . $task->id);
                exit();
              } else {
                throw new Exception('You cannot edit a task unless you are assigned to it or are the project lead');
              }
            } else {
              throw new Exception('You cannot edit a task from a team you are not in');
            }
          } else {
            throw new Exception('Could not find task');
          }
        } catch (Exception $err) {
          Session::set('error', $err->getMessage());
        }

        header('Location: ../tasks');
        exit();
        break;
      }
      case $IS_DELETING:
      {
        $user = Session::get('user');
        $id = Request::post('id');

        try {
          $task = Task::get($id);
          if ($task !== null) {
            if ($user->id === $task->id || $user->id === $task->project->lead->id) {
              $task->remove();
              Session::unset('error');
            } else {
              throw new Exception('You cannot delete a task unless you are assigned to it or are the project lead');
            }
          } else {
            throw new Exception('Could not find task');
          }
        } catch (Exception $err) {
          Session::set('error', $err->getMessage());
        }

        header('Location: ../tasks');
        exit();
        break;
      }
    }
  }

  /**
   * @throws Exception
   */
  private static function add(
    string        $name,
    string        $description,
    Project       $project,
    ?User         $user = null,
    ?TaskStatus   $status = TaskStatus::NotStarted,
    ?TaskPriority $priority = TaskPriority::Low
  ): Task|null
  {
    $id = Database::generate_id();
    var_dump($status->value);
    $is_insert_successful = Database::query('
      INSERT INTO `tasks`
      VALUES (?, ?, ?, ?, ?, ?, ?)
    ', [
        $id,
        $name,
        $description,
        $status->value,
        $priority->value,
        $user?->id,
        $project->id
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
        $task['user_id'] ? User::get($task['user_id']) : null
      );
    } else {
      return null;
    }
  }

  /**
   * @throws Exception
   */
  private function update(
    ?string       $name,
    ?string       $description,
    ?User         $user,
    ?TaskStatus   $status,
    ?TaskPriority $priority
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
  private function remove(): bool
  {
    return Database::query('
      DELETE 
      FROM `tasks`
      WHERE `id` = ?
    ', [$this->id])["affected_rows"] == 1;
  }

  /**
   * @throws Exception
   */
  public static function search(
    string $query,
    ?array $statuses = [
      TaskStatus::NotStarted->value,
      TaskStatus::InProgress->value,
      TaskStatus::Completed->value
    ],
    ?array $priorities = [
      TaskPriority::Low->value,
      TaskPriority::High->value
    ],
    ?bool  $in_name = true,
    ?bool  $in_description = true
  ): array
  {
    $statuses_placeholder = implode(',', array_fill(0, count($statuses), '?'));
    $priorities_placeholder = implode(',', array_fill(0, count($priorities), '?'));
    $tasks_result = Database::query("
      SELECT `id`
      FROM `tasks`
      WHERE ((
          ? AND LOWER(`name`) LIKE ?
        ) OR (
          ? AND LOWER(`description`) LIKE ?
        )
      ) AND (
        ? OR `status` IN ($statuses_placeholder)
      ) AND (
        ? OR `priority` IN ($priorities_placeholder)
      )
    ", array_merge(
      [$in_name, strtolower('%' . $query . '%'),
        $in_description, strtolower('%' . $query . '%')],
      [$statuses === null], $statuses,
      [$priorities === null], $priorities
    ))["result"];
    $tasks = [];
    foreach ($tasks_result as $task_result) {
      $tasks[] = Task::get($task_result['id']);
    }
    return $tasks;
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
