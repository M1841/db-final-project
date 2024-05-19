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
try {
  $id = Request::get('id');
  if ($id === null) {
    header('Location: ../teams');
    exit();
  }
  $team = Team::get($id);
  if ($team === null) {
    throw new Exception('Could not find team');
  }
  if (!$user->is_in_team($team)) {
    throw new Exception('You cannot view a team you are not in');
  }

  $members = $team->get_members();
  $projects = $team->get_projects();
} catch (Exception $err) {
  Session::set('error', $err->getMessage());
  header('Location: ../teams');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?= $team->name ?></title>
</head>
<body>
  <p><?= $team->name ?></p>
  <p><?= $team->description ?></p>

  <p>Edit Team</p>
  <form method="POST" action="../api/Team.php">
    <input type="hidden" name="resource" value="team"/>
    <input type="hidden" name="action" value="edit"/>
    <input type="hidden" name="id" value="<?= $team->id ?>"/>

    <input type="text" name="name" placeholder="Name" required
      value="<?= $team->name ?>"/>
    <textarea name="description" placeholder="Description"
    ><?= $team->description ?></textarea>

    <input type="submit" value="Edit"/>
  </form>

  <p>Delete Team</p>
  <form method="POST" action="../api/Team.php">
    <input type="hidden" name="resource" value="team"/>
    <input type="hidden" name="action" value="delete"/>
    <input type="hidden" name="id" value="<?= $team->id ?>"/>
    <input type="submit" value="Delete"/>
  </form>

  <p>Members</p>
  <ul>
    <?php foreach ($members as $member) { ?>
      <li>
        <p><?= $member->name ?></p>
      </li>
    <?php } ?>
  </ul>

  <p>Projects</p>
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

  <p>Create a Project</p>
  <form method="POST" action="../api/Project.php">
    <input type="hidden" name="resource" value="project"/>
    <input type="hidden" name="action" value="create"/>
    <input type="hidden" name="team_id" value="<?= $team->id ?>"/>
    <input type="text" name="name" placeholder="Name" required/>
    <textarea name="description" placeholder="Description"></textarea>
    <input type="submit" value="Create"/>
  </form>

  <?php
  echo(Session::get('error'));
  Session::unset('error');
  ?>
</body>
</html>