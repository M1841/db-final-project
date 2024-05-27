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
$query = Request::get('search');
if ($query) {
  $teams = Team::search($query, !Request::get('name'), !Request::get('description'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Teams</title>

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
      <h2>Your Teams</h2>
      <?php require_once __DIR__ . '/../components/teams.php' ?>
    </section>
    <footer>
      <button data-modal-target="search_modal" data-modal-toggle="search_modal">
        <i class="symbol">search</i>
        Search Teams
      </button>
      <button data-modal-target="join_modal" data-modal-toggle="join_modal">
        <i class="symbol">input_circle</i>
        Join a Team
      </button>
      <button data-modal-target="create_modal" data-modal-toggle="create_modal">
        <i class="symbol">add</i>
        Create a Team
      </button>

      <div id="search_modal" tabindex="-1" class="hidden">
        <form method="GET" action="./teams">
          <h3>Search Teams</h3>
          <label for="team_query">
            <input type="text" name="search" required id="team_query"
              placeholder="Search Teams" value="<?= $query ?>"/>
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
            <a href="./teams">Clear</a>
          </div>
        </form>
      </div>
      <div id="join_modal" tabindex="-1" class="hidden">
        <form method="POST" action="../api/Team.php">
          <h3>Join a Team</h3>
          <input type="hidden" name="resource" value="team"/>
          <input type="hidden" name="action" value="join"/>
          <label for="team_code">
            <input type="text" name="code" placeholder="Team Code"
              id="team_code" minlength="36" maxlength="36" required/>
          </label>
          <input type="submit" value="Join"/>
        </form>
      </div>
      <div id="create_modal" tabindex="-1" class="hidden">
        <form method="POST" action="../api/Team.php">
          <h3>Create a Team</h3>
          <input type="hidden" name="resource" value="team"/>
          <input type="hidden" name="action" value="create"/>
          <label for="team_name">
            <input type="text" name="name" placeholder="Team Name"
              required id="team_name"/>
          </label>
          <label for="team_description">
            <textarea name="description" placeholder="Team Description"
              id="team_description"></textarea>
          </label>
          <input type="submit" value="Create"/>
        </form>
      </div>
    </footer>
    <?php require_once __DIR__ . '/../components/error.php' ?>
  </main>
</body>