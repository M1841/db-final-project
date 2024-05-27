<?php
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Team.php';
require_once __DIR__ . '/Request.php';
require_once __DIR__ . '/Session.php';

readonly class Project
{
  public function __construct(
    public string $id,
    public string $name,
    public string $description,
    public User   $lead,
    public Team   $team,
  ) {}

  public static function router(): void
  {
    $IS_CREATING = Request::method() === 'POST'
      && Session::get('user') !== null
      && Request::post('team_id')
      && Request::post('name')
      && Request::post('action') === 'create'
      && Request::post('resource') === 'project';

    $IS_EDITING = Request::method() === 'POST'
      && Session::get('user') !== null
      && Request::post('id')
      && Request::post('name')
      && Request::post('action') === 'edit'
      && Request::post('resource') === 'project';

    $IS_DELETING = Request::method() === 'POST'
      && Session::get('user') !== null
      && Request::post('id')
      && Request::post('action') === 'delete'
      && Request::post('resource') === 'project';

    switch (true) {
      case $IS_CREATING:
      {
        $team_id = Request::post('team_id');
        $name = Request::post('name');
        $description = Request::post('description');
        $user = Session::get('user');

        try {
          $team = Team::get($team_id);
          if ($team !== null) {
            if ($user->is_in_team($team)) {
              $project = Project::add($name, $description, $user, $team);
              Session::unset('error');

              header('Location: ../project?id=' . $project->id);
              exit();
            } else {
              throw new Exception('You cannot add a project to a team you are not in');
            }
          } else {
            throw new Exception('Could not find team');
          }
        } catch (Exception $err) {
          Session::set('error', $err->getMessage());
        }

        header('Location: ../projects');
        exit();
        break;
      }
      case $IS_EDITING:
      {
        $id = Request::post('id');
        $name = Request::post('name');
        $description = Request::post('description');
        $user = Session::get('user');

        try {
          $project = Project::get($id);
          if ($project !== null) {
            if ($user->id === $project->lead->id) {
              $project->update($name, $description);
              Session::unset('error');

              header('Location: ../project?id=' . $project->id);
              exit();
            } else {
              throw new Exception('You cannot edit a project if you are not its lead');
            }
          } else {
            throw new Exception('Could not find project');
          }
        } catch (Exception $err) {
          Session::set('error', $err->getMessage());
        }

        header('Location: ../projects');
        exit();
        break;
      }
      case $IS_DELETING:
      {
        $user = Session::get('user');
        $id = Request::post('id');

        try {
          $project = Project::get($id);
          if ($project !== null) {
            if ($user->id === $project->lead->id) {
              $project->remove();
              Session::unset('error');
            } else {
              throw new Exception('You cannot delete a project if you are not its lead');
            }
          } else {
            throw new Exception('Could not find project');
          }
        } catch (Exception $err) {
          Session::set('error', $err->getMessage());
        }

        header('Location: ../projects');
        exit();
        break;
      }
    }
  }

  /**
   * @throws Exception
   */
  private static function add(
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
      SELECT `id`, `name`, `description`, `status`, `priority`, `user_id`
      FROM `tasks`
      WHERE `project_id` = ?
      ORDER BY `user_id`, `priority`, `status`, `name`, `description`, `id`
    ', [$this->id])["result"];

    $tasks = [];
    while ($task_result = $tasks_result->fetch_assoc()) {
      $tasks[] = new Task(
        $task_result["id"],
        $task_result["name"],
        $task_result["description"],
        $this,
        TaskStatus::tryFrom($task_result["status"]),
        TaskPriority::tryFrom($task_result["priority"]),
        $task_result['user_id'] ? User::get($task_result['user_id']) : null
      );
    }

    return $tasks;
  }

  /**
   * @throws Exception
   */
  private function update(
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
  private function remove(): bool
  {
    return Database::query('
      DELETE
      FROM `projects`
      WHERE `id` = ?
    ', [$this->id])["affected_rows"] == 1;
  }

  /**
   * @throws Exception
   */
  public static function search(
    string $query,
    ?bool  $in_name = true,
    ?bool  $in_description = true
  ): array
  {
    $projects_result = Database::query('
      SELECT `id`, `name`, `description`, `team_id`, `lead_id`
      FROM `projects`
      WHERE (
        ? AND LOWER(`name`) LIKE ?
      ) OR (
        ? AND LOWER(`description`) LIKE ?
      )
      ORDER BY `team_id`, `lead_id`, `name`, `description`, `id`
    ', [
      $in_name, strtolower('%' . $query . '%'),
      $in_description, strtolower('%' . $query . '%')
    ])["result"];
    $projects = [];
    foreach ($projects_result as $project_result) {
      $projects[] = new Project(
        $project_result["id"],
        $project_result["name"],
        $project_result["description"],
        User::get($project_result["lead_id"]),
        Team::get($project_result["team_id"])
      );
    }
    return $projects;
  }
}

Project::router();
