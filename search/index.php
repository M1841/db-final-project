<?php
require_once __DIR__ . '/../api/User.php';
require_once __DIR__ . '/../api/Team.php';
require_once __DIR__ . '/../api/Project.php';
require_once __DIR__ . '/../api/Task.php';
require_once __DIR__ . '/../api/Request.php';
require_once __DIR__ . '/../api/Session.php';

$user = Session::get('user');
if ($user === null) {
  header('Location: ./auth');
  exit();
}
$query = Request::get('query') ?? '';
$teams = Team::search($query, $user->get_teams());
$projects = Project::search($query, $user->get_projects());
$tasks = Task::search($query, $user->get_tasks());
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
  <title>Search</title>
</head>
<body>
  <h2>Search</h2>
  <form method="GET" action="./search">
    <input type="text" name="query" required
      placeholder="Search" value="<?= $query ?>"/>
    <input type="submit" class="symbol" value="search"/>
    <a href="./search">Clear</a>
  </form>
  <main>
    <section>
      <h2>Teams</h2>
      <?php foreach ($teams as $team) { ?>
        <a href="./team?id=<?= $team->id ?>">
          <?= $team->name ?>
          <?= $team->description ?>
        </a>
        <br/>
      <?php } ?>
    </section>

    <section>
      <h2>Projects</h2>
      <?php foreach ($projects as $project) { ?>
        <a href="./project?id=<?= $project->id ?>">
          <?= $project->name ?>
          <?= $project->description ?>
        </a>
        <br/>
      <?php } ?>
    </section>

    <section>
      <h2>Tasks</h2>
      <?php foreach ($tasks as $task) { ?>
        <a href="./task?id=<?= $task->id ?>">
          <?= $task->name ?>
          <?= $task->description ?>
        </a>
        <br/>
      <?php } ?>
    </section>
  </main>
</body>
</html>
