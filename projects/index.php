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
$query = Request::get('query');
if ($query) {
  $projects = Project::search($query, $projects, !Request::get('name'), !Request::get('description'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Projects</title>
</head>
<body>
  <form method="GET" action="./projects">
    <input type="text" name="query" required
      placeholder="Search" value="<?= $query ?>"/>
    <input type="submit" class="symbol" value="search"/>
    <a href="./projects">Clear</a>
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
    </fieldset>
  </form>

  <p>Create a Project</p>
  <form method="POST" action="../api/Project.php">
    <input type="hidden" name="resource" value="project"/>
    <input type="hidden" name="action" value="create"/>
    <select name="team_id" required>
      <?php foreach ($teams as $team) { ?>
        <option value="<?= $team->id ?>">
          <?= $team->name ?>
        </option>
      <?php } ?>
    </select>
    <input type="text" name="name" placeholder="Name" required/>
    <textarea name="description" placeholder="Description"></textarea>
    <input type="submit" value="Create"/>
  </form>

  <p>Your Projects</p>
  <ul>
    <?php foreach ($projects as $project) { ?>
      <li>
        <a href="../project?id=<?= $project->id ?>">
          <p><?= $project->name ?></p>
          <p><?= $project->description ?></p>
        </a>
      </li>
    <?php } ?>
  </ul>
  <?php
  echo(Session::get('error'));
  Session::unset('error');
  ?>
</body>