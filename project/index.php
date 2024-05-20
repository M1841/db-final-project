<?php
require_once __DIR__ . '/../api/User.php';
require_once __DIR__ . '/../api/Team.php';
require_once __DIR__ . '/../api/Project.php';
require_once __DIR__ . '/../api/Task.php';
require_once __DIR__ . '/../api/Request.php';
require_once __DIR__ . '/../api/Session.php';

$user = Session::get('user');
if ($user === null) {
  header('Location: ../auth');
  exit();
}
try {
  $id = Request::get('id');
  if ($id === null) {
    header('Location: ../projects');
    exit();
  }
  $project = Project::get($id);
  if ($project === null) {
    throw new Exception('Could not find project');
  }
  $team = $project->team;
  if (!$user->is_in_team($team)) {
    throw new Exception('You cannot view a project from a team you are not in');
  }
  $members = $project->team->get_members();
  $tasks = $project->get_tasks();
} catch (Exception $err) {
  Session::set('error', $err->getMessage());
  header('Location: ../projects');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?= $project->name ?></title>
</head>
<body>
  <p><?= $project->name ?></p>
  <p><?= $project->description ?></p>

  <p>Edit Project</p>
  <form method="POST" action="../api/Project.php">
    <input type="hidden" name="resource" value="project"/>
    <input type="hidden" name="action" value="edit"/>
    <input type="hidden" name="id" value="<?= $project->id ?>"/>

    <input type="text" name="name" placeholder="Name" required
      value="<?= $project->name ?>"/>
    <textarea name="description" placeholder="Description"
    ><?= $project->description ?></textarea>

    <input type="submit" value="Edit"/>
  </form>

  <p>Delete Project</p>
  <form method="POST" action="../api/Project.php">
    <input type="hidden" name="resource" value="project"/>
    <input type="hidden" name="action" value="delete"/>
    <input type="hidden" name="id" value="<?= $project->id ?>"/>
    <input type="submit" value="Delete"/>
  </form>


  <p>Tasks</p>
  <ul>
    <?php foreach ($tasks as $task) { ?>
      <li>
        <a href="../task?id=<?= $task->id ?>">
          <p><?= $task->name ?></p>
          <p><?= $task->description ?></p>
        </a>
      </li>
    <?php } ?>
  </ul>

  <p>Create a Task</p>
  <form method="POST" action="../api/Task.php">
    <input type="hidden" name="resource" value="task"/>
    <input type="hidden" name="action" value="create"/>
    <input type="hidden" name="project_id" value="<?= $project->id ?>"/>

    <input type="text" name="name" placeholder="Name" required/>
    <textarea name="description" placeholder="Description"></textarea>

    <select name="priority" required>
      <?php foreach (TaskPriority::cases() as $case) { ?>
        <option value="<?= $case->value ?>">
          <?= $case->value ?>
        </option>
      <?php } ?>
    </select>

    <select name="status" required>
      <?php foreach (TaskStatus::cases() as $case) { ?>
        <option value="<?= $case->value ?>">
          <?= $case->value ?>
        </option>
      <?php } ?>
    </select>

    <select name="user_id" required>
      <option value="null">Unassigned</option>
      <?php foreach ($members as $member) { ?>
        <option value="<?= $member->id ?>">
          <?= $member->name ?>
        </option>
      <?php } ?>
    </select>
    <input type="submit" value="Create"/>
  </form>

  <?php
  echo(Session::get('error'));
  Session::unset('error');
  ?>
</body>