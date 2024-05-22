<?php
require_once __DIR__ . '/../api/User.php';
require_once __DIR__ . '/../api/Team.php';
require_once __DIR__ . '/../api/Request.php';
require_once __DIR__ . '/../api/Session.php';

$user = Session::get('user');
if ($user === null) {
  header('Location: ../auth');
  exit();
}
$teams = $user->get_teams();
$query = Request::get('query');
if ($query) {
  $teams = Team::search($query, $teams, !Request::get('name'), !Request::get('description'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Teams</title>
</head>
<body>
  <form method="GET" action="./teams">
    <input type="text" name="query" required
      placeholder="Search" value="<?= $query ?>"/>
    <input type="submit" class="symbol" value="search"/>
    <a href="./teams">Clear</a>
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

  <p>Join a Team</p>
  <form method="POST" action="../api/Team.php">
    <input type="hidden" name="resource" value="team"/>
    <input type="hidden" name="action" value="join"/>
    <input type="text" name="code" placeholder="Code"
      minlength="36" maxlength="36" required/>
    <input type="submit" value="Join"/>
  </form>

  <p>Create a Team</p>
  <form method="POST" action="../api/Team.php">
    <input type="hidden" name="resource" value="team"/>
    <input type="hidden" name="action" value="create"/>
    <input type="text" name="name" placeholder="Name" required/>
    <textarea name="description" placeholder="Description"></textarea>
    <input type="submit" value="Create"/>
  </form>

  <p>Your Teams</p>
  <ul>
    <?php foreach ($teams as $team) { ?>
      <li>
        <a href="../team?id=<?= $team->id ?>">
          <p><?= $team->name ?></p>
          <p><?= $team->description ?></p>
        </a>
      </li>
    <?php } ?>
  </ul>
  <?php
  echo(Session::get('error'));
  Session::unset('error');
  ?>
</body>