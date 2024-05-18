<?php
require_once __DIR__ . '/../api/User.php';
require_once __DIR__ . '/../api/Team.php';
require_once __DIR__ . '/../api/Session.php';

$user = Session::get('user');

if ($user === null) {
  header('Location: ../auth');
  exit();
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
  <p>Join a Team</p>
  <form method="POST" action="../api/Team.php">
    <input type="hidden" name="action" value="join"/>
    <input type="text" name="code" placeholder="Code"
      minlength="36" maxlength="36" required/>
    <input type="submit" value="Join"/>
  </form>

  <p>Create a Team</p>
  <form method="POST" action="../api/Team.php">
    <input type="hidden" name="action" value="create"/>
    <input type="text" name="name" placeholder="Name" required/>
    <textarea name="description" placeholder="Description"></textarea>
    <input type="submit" value="Create"/>
  </form>

  <p>Your Teams</p>
  <ul>
    <?php foreach ($user->get_teams() as $team) { ?>
      <li>
        <a href="../team?id=<?= $team->id ?>">
          <p><?= $team->name ?></p>
          <p><?= $team->description ?></p>
        </a>
      </li>
    <?php } ?>
  </ul>
  <?php echo(Session::get('error')); ?>
</body>