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

$teams = array_slice($user->get_teams(), 0, 4);
$projects = array_slice($user->get_projects(), 0, 4);
$tasks = array_slice($user->get_tasks(), 0, 4);
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

  <script src="./lib/tailwind.min.js"></script>
  <script src="./lib/flowbite.min.js"></script>
</head>

<body>
  <header>
    <nav>
      <h1>
        <i class="symbol">inventory</i>
        App Title
      </h1>

      <a href="./teams">Teams</a>
      <a href="./projects">Projects</a>
      <a href="./tasks">Tasks</a>
    </nav>

    <form method="GET" action="./search">
      <label for="query">
        <input type="text" id="search" name="query" required placeholder="Search"/>
      </label>
      <input type="submit" class="symbol" value="search"/>
    </form>

    <button data-dropdown-toggle="account_dropdown">
      <div>
        <span><?= mb_strtoupper($user->name[0]) ?></span>
      </div>
      <?= $user->name ?>

      <i class="symbol">unfold_more</i>
    </button>

    <div id="account_dropdown" class="hidden">
      <form method="POST" action="./api/User.php">
        <input type="hidden" name="action" required value="logout"/>
        <input type="submit" value="Logout"/>
      </form>
    </div>
  </header>

  <main>
    <section>
      <h2>Teams</h2>
      <div>
        <?php foreach ($teams as $team) {
          $member_count = count($team->get_members()); ?>
          <a href="./team?id=<?= $team->id ?>">
            <div>
              <h3><?= $team->name ?></h3>
              <span>
                <?= $member_count ?>
                Member<?= $member_count == 1 ? '' : 's' ?>
              </span>
            </div>
            <p><?= $team->description ?></p>
          </a>
        <?php } ?>
      </div>
    </section>

    <section>
      <h2>Projects</h2>
      <div>
        <?php foreach ($projects as $project) {
          $task_count = count($project->get_tasks()); ?>
          <a href="./project?id=<?= $project->id ?>">
            <div>
              <h3><?= $project->name ?></h3>
              <span>
                <?= $task_count ?>
                Task<?= $task_count == 1 ? '' : 's' ?>
              </span>
            </div>
            <p><?= $project->description ?></p>
          </a>
        <?php } ?>
      </div>
    </section>

    <section>
      <h2>Tasks</h2>
      <div>
        <?php foreach ($tasks as $task) { ?>
          <a href="./task?id=<?= $task->id ?>">
            <div>
              <h3><?= $task->name ?></h3>
              <span>
                <?= $task->status->value ?>
              </span>
            </div>
            <p><?= $task->description ?></p>
          </a>
        <?php } ?>
      </div>
    </section>
  </main>

  <?php
  echo(Session::get('error'));
  Session::unset('error');
  ?>
</body>
</html>