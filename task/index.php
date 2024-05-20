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
    header('Location: ../tasks');
    exit();
  }
  $task = Task::get($id);
  if ($task === null) {
    throw new Exception('Could not find task');
  }
  $project = $task->project;
  $team = $project->team;
  $members = $team->get_members();
  if (!$user->is_in_team($team)) {
    throw new Exception('You cannot view a task from a team you are not in');
  }
} catch (Exception $err) {
  Session::set('error', $err->getMessage());
  header('Location: ../tasks');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?= $task->name ?></title>
</head>
<body>
  <p><?= $task->name ?></p>
  <p><?= $task->description ?></p>

  <p>Edit Task</p>
  <form method="POST" action="../api/Task.php">
    <input type="hidden" name="resource" value="task"/>
    <input type="hidden" name="action" value="edit"/>
    <input type="hidden" name="id" value="<?= $task->id ?>"/>

    <input type="text" name="name" placeholder="Name" required
      value="<?= $task->name ?>"/>
    <textarea name="description" placeholder="Description"
    ><?= $task->description ?></textarea>

    <select name="priority" required>
      <?php foreach (TaskPriority::cases() as $case) { ?>
        <option value="<?= $case->value ?>"
          <?= $task->priority === $case ? 'selected' : '' ?>
        >
          <?= $case->value ?>
        </option>
      <?php } ?>
    </select>

    <select name="status" required>
      <?php foreach (TaskStatus::cases() as $case) { ?>
        <option value="<?= $case->value ?>"
          <?= $task->status === $case ? 'selected' : '' ?>
        >
          <?= $case->value ?>
        </option>
      <?php } ?>
    </select>

    <select name="user_id" required>
      <option value="null">Unassigned</option>
      <?php foreach ($members as $member) { ?>
        <option value="<?= $member->id ?>"
          <?= $task->user->id === $member->id ? 'selected' : '' ?>
        >
          <?= $member->name ?>
        </option>
      <?php } ?>
    </select>

    <input type="submit" value="Edit"/>
  </form>

  <p>Delete Task</p>
  <form method="POST" action="../api/Task.php">
    <input type="hidden" name="resource" value="task"/>
    <input type="hidden" name="action" value="delete"/>
    <input type="hidden" name="id" value="<?= $task->id ?>"/>
    <input type="submit" value="Delete"/>
  </form>

  <?php
  echo(Session::get('error'));
  Session::unset('error');
  ?>
</body>