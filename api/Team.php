<?php
require_once __DIR__ . '/User.php';
require_once __DIR__ . '/Session.php';

readonly class Team
{
  public function __construct(
    public string $id,
    public string $name,
    public string $description
  ) {}

  public static function router(): void
  {
    define('IS_CREATING',
      $_SERVER['REQUEST_METHOD'] === 'POST'
      && Session::get('user') !== null
      && isset($_POST['name'])
      && isset($_POST['action'])
      && $_POST['action'] === 'create'
    );

    define('IS_JOINING',
      $_SERVER['REQUEST_METHOD'] === 'POST'
      && Session::get('user') !== null
      && isset($_POST['code'])
      && isset($_POST['action'])
      && $_POST['action'] === 'join'
    );

    switch (true) {
      case IS_CREATING:
      {
        $name = $_POST['name'];
        $description = $_POST['description'];
        $user = Session::get('user');

        try {
          $team = Team::add($name, $description);
          if (!$user->join($team)) {
            $team->remove();
            throw new Exception('An unexpected error occurred, try again later');
          }
          Session::unset('error');
        } catch (Exception $err) {
          Session::set('error', $err->getMessage());
        }

        header('Location: ../teams');
        exit();

        break;
      }

      case IS_JOINING:
      {
        $id = $_POST['code'];
        $user = Session::get('user');

        try {
          $team = Team::get($id);
          if ($team !== null) {
            if (!$user->join($team)) {
              throw new Exception('An unexpected error occurred, try again later');
            }
            Session::unset('error');
          } else {
            throw new Exception('Could not find a team matching that code');
          }
        } catch (Exception $err) {
          Session::set('error', $err->getMessage());
        }

        header('Location: ../teams');
        exit();

        break;
      }
    }
  }

  /**
   * @throws Exception
   */
  public static function add(
    string $name,
    string $description
  ): Team|null
  {
    $id = Database::generate_id();

    $is_insert_successful = Database::query('
      INSERT INTO `teams`
      VALUES (?, ?, ?)
    ', [$id, $name, $description])["affected_rows"] == 1;

    return $is_insert_successful ? Team::get($id) : null;
  }

  /**
   * @throws Exception
   */
  public static function get(string $id): Team|null
  {
    $team_result = Database::query('
      SELECT `name`, `description`
      FROM `teams`
      WHERE `id` = ?
    ', [$id])["result"];

    if ($team_result->num_rows == 1) {
      $team = $team_result->fetch_assoc();

      return new Team(
        $id,
        $team['name'],
        $team['description']
      );
    } else {
      return null;
    }
  }

  /**
   * @throws Exception
   */
  public function get_members(): array
  {
    $members_result = Database::query('
      SELECT `users`.`id`
      FROM `_member_of_`
      JOIN `users`
        ON `_member_of_`.`user_id` = `users`.`id`
      WHERE `_member_of_`.`team_id` = ?
    ', [$this->id])["result"];


    $members = array();
    while ($member_result = $members_result->fetch_assoc()) {
      $members[] = User::get($member_result['id']);
    }

    return $members;
  }

  /**
   * @throws Exception
   */
  public function get_projects(): array
  {
    $projects_result = Database::query('
      SELECT `id`
      FROM `projects`
      WHERE `team_id` = ?
    ', [$this->id])["result"];

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
    $is_update_successful = Database::query('
      UPDATE `teams`
        SET `name` = ?,
            `description` = ?
      WHERE `id` = ?
    ', [
      $name ?? $this->name,
      $description ?? $this->description,
      $this->id
    ])["affected_rows"];

    return $is_update_successful ? new Team(
      $this->id,
      $name ?? $this->name,
      $description ?? $this->description
    ) : $this;
  }

  /**
   * @throws Exception
   */
  public function remove(): bool
  {
    return Database::query('
      DELETE
      FROM `teams`
      WHERE `id` = ?
    ', [$this->id])["affected_rows"] == 1;
  }
}

Team::router();
