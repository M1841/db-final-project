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
$query = Request::get('search');
if ($query) {
  $projects = Project::search($query, !Request::get('name'), !Request::get('description'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Projects</title>

  <link rel="stylesheet" href="../css/base.css"/>
  <link rel="stylesheet" href="../css/main.css"/>
  <link rel="stylesheet" href="../css/index.css"/>
  <link rel="stylesheet" href="../css/navbar.css"/>
  <link rel="stylesheet" href="../css/resources.css"/>

  <script src="../lib/tailwind.min.js"></script>
  <script src="../lib/flowbite.min.js"></script>
</head>
<body>
  <?php require_once __DIR__ . '/../components/navbar.php' ?>
  <main>
    <section>
      <h2>Your Projects</h2>
      <?php require_once __DIR__ . '/../components/projects.php' ?>
    </section>
    <footer>
      <button data-modal-target="search_modal" data-modal-toggle="search_modal">
        <i class="symbol">search</i>
        Search Projects
      </button>
      <button data-modal-target="create_modal" data-modal-toggle="create_modal">
        <i class="symbol">add</i>
        Create a Project
      </button>

      <div id="search_modal" tabindex="-1" class="hidden">
        <form method="GET" action="./projects">
          <h3>Search Projects</h3>
          <label for="project_query">
            <input type="text" name="search" required id="project_query"
              placeholder="Search Projects" value="<?= $query ?>"/>
          </label>
          <fieldset>
            <label for="name">
              <input type="checkbox" name="name" id="name"
                value="false"
                <?= Request::get('name') ? 'checked' : '' ?>/>
              Ignore Name
            </label>
            <label for="description">
              <input type="checkbox" name="description" id="description"
                value="false"
                <?= Request::get('description') ? 'checked' : '' ?>/>
              Ignore Description
            </label>
          </fieldset>
          <div>
            <input type="submit" value="Search"/>
            <a href="./projects">Clear</a>
          </div>
        </form>
      </div>
      <div id="create_modal" tabindex="-1" class="hidden">
        <form method="POST" action="../api/Project.php">
          <h3>Create a Project</h3>
          <input type="hidden" name="resource" value="project"/>
          <input type="hidden" name="action" value="create"/>
          <label for="project_team_id">
            <select name="team_id" required id="project_team_id">
              <option value="">Choose a Team</option>
              <?php foreach ($teams as $team) { ?>
                <option value="<?= $team->id ?>">
                  <?= $team->name ?>
                </option>
              <?php } ?>
            </select>
          </label>
          <label for="project_name">
            <input type="text" name="name" placeholder="Project Name"
              required id="project_name"/>
          </label>
          <label for="project_description">
            <textarea name="description" placeholder="Project Description"
              id="project_description"></textarea>
          </label>
          <input type="submit" value="Create"/>
        </form>
      </div>
    </footer>
    <?php require_once __DIR__ . '/../components/error.php' ?>
  </main>

</body>