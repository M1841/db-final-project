<?php
require_once __DIR__ . '/api/User.php';
require_once __DIR__ . '/api/Team.php';
require_once __DIR__ . '/api/Project.php';
require_once __DIR__ . '/api/Task.php';
require_once __DIR__ . '/api/Session.php';

$user = Session::get('user');
if ($user === null) {
  header('Location: ./auth');
  exit();
}

if ($query = Request::get('search')) {
  $teams = Team::search($query, $user->get_teams());
  $projects = Project::search($query, $user->get_projects());
  $tasks = Task::search($query, $user->get_tasks());
} else {
  $teams = array_slice($user->get_teams(), 0, 4);
  $projects = array_slice($user->get_projects(), 0, 4);
  $tasks = array_slice($user->get_tasks(), 0, 4);
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Home</title>

  <link rel="stylesheet" href="css/base.css"/>
  <link rel="stylesheet" href="css/index.css"/>
  <link rel="stylesheet" href="css/navbar.css"/>
  <link rel="stylesheet" href="css/resources.css"/>

  <script src="./lib/tailwind.min.js"></script>
  <script src="./lib/flowbite.min.js"></script>
</head>

<body>
  <?php
  require_once __DIR__ . '/components/navbar.php';
  require_once __DIR__ . '/components/resources.php';
  ?>
</body>
</html>