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
$teams = $user->get_teams();
$projects = $user->get_projects();
$tasks = $user->get_tasks();
$query = Request::get('query');
$statuses = Request::get_array('status');
$priorities = Request::get_array('priority');
if ($query) {
  $tasks = Task::search(
    $query, $tasks,
    $statuses, $priorities,
    !Request::get('name'), !Request::get('description'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Tasks</title>
</head>
<body>
  <form method="GET" action="./tasks">
    <input type="text" name="query" required
      placeholder="Search" value="<?= $query ?>"/>
    <input type="submit" class="symbol" value="search"/>
    <a href="./tasks">Clear</a>
    <fieldset>
      <legend>Filters</legend>
      <label for="name">
        <input type="checkbox" name="name" id="name"
          value="false" <?= Request::get('name') ? 'checked' : '' ?>/>
        Ignore Name
      </label>
      <label for="description">
        <input type="checkbox" name="description" id="description"
          value="false" <?= Request::get('description') ? 'checked' : '' ?>/>
        Ignore Description
      </label>
      <fieldset>
        <legend>Status</legend>
        <?php foreach (TaskStatus::cases() as $case) { ?>
          <label for="<?= $case->value ?>">
            <input type="checkbox" name="status[]"
              id="<?= $case->value ?>" value="<?= $case->value ?>"
              <?= in_array($case->value, $statuses ?? TaskStatus::cases())
                ? 'checked' : '' ?>/>
            <?= $case->value ?>
          </label>
        <?php } ?>
      </fieldset>
      <fieldset>
        <legend>Priority</legend>
        <?php foreach (TaskPriority::cases() as $case) { ?>
          <label for="<?= $case->value ?>">
            <input type="checkbox" name="priority[]"
              id="<?= $case->value ?>" value="<?= $case->value ?>"
              <?= in_array($case->value, $priorities ?? TaskPriority::cases())
                ? 'checked' : '' ?>/>
            <?= $case->value ?>
          </label>
        <?php } ?>
      </fieldset>
    </fieldset>
  </form>

  <p>Create a Task</p>
  <form method="POST" action="../api/Task.php">
    <input type="hidden" name="resource" value="task"/>
    <input type="hidden" name="action" value="create"/>
    <input type="hidden" name="user_id" value="null"/>
    <select name="project_id" required>
      <?php foreach ($projects as $project) { ?>
        <option value="<?= $project->id ?>">
          <?= $project->name ?>
        </option>
      <?php } ?>
    </select>
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

    <input type="submit" value="Create"/>
  </form>

  <p>Your Tasks</p>
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
  <?php
  echo(Session::get('error'));
  Session::unset('error');
  ?>
</body>