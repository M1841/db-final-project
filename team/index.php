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
    <header>
      <h1>
        <i class="symbol">group</i>
        <?= $team->name ?>
      </h1>
      <p><?= $team->description ?></p>
    </header>
    <div>
      <aside>
        <h2>Members</h2>
        <div>
          <?php foreach ($members as $member) { ?>
            <div>
              <div>
                <span><?= mb_strtoupper($member->name[0]) ?></span>
              </div>
              <?= $member->name ?>
            </div>
          <?php } ?>
        </div>
      </aside>
      <section>
        <h2>Projects</h2>
        <?php require_once __DIR__ . '/../components/projects.php' ?>
      </section>
    </div>

    <footer>
      <button onclick="copy_team_code()" id="copy_button">
        <i class="symbol">content_copy</i>
        <span>Copy Code</span>
      </button>
      <button data-modal-target="edit_modal" data-modal-toggle="edit_modal">
        <i class="symbol">edit</i>
        Edit Team
      </button>
      <button data-modal-target="create_modal" data-modal-toggle="create_modal">
        <i class="symbol">add</i>
        Create a Project
      </button>
      <button data-modal-target="delete_modal"
        data-modal-toggle="delete_modal">
        <i class="symbol">delete</i>
        Delete Team
      </button>

      <div id="edit_modal" tabindex="-1" class="hidden">
        <form method="POST" action="../api/Team.php">
          <h3>Edit Team</h3>
          <input type="hidden" name="resource" value="team"/>
          <input type="hidden" name="action" value="edit"/>
          <input type="hidden" name="id" value="<?= $team->id ?>"/>
          <label for="team_name">
            <input type="text" name="name" placeholder="Team Name"
              required id="team_name" value="<?= $team->name ?>"/>
          </label>
          <label for="team_description">
            <textarea name="description" placeholder="Team Description"
              id="team_description"><?= $team->description ?></textarea>
          </label>
          <input type="submit" value="Edit"/>
        </form>
      </div>

      <div id="create_modal" tabindex="-1" class="hidden">
        <form method="POST" action="../api/Project.php">
          <h3>Create a Project</h3>
          <input type="hidden" name="resource" value="project"/>
          <input type="hidden" name="action" value="create"/>
          <input type="hidden" name="team_id" value="<?= $team->id ?>"/>
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

      <div id="delete_modal" tabindex="-1" class="hidden">
        <form method="POST" action="../api/Team.php">
          <h3>Are you sure?</h3>
          <input type="hidden" name="resource" value="team"/>
          <input type="hidden" name="action" value="delete"/>
          <input type="hidden" name="id" value="<?= $team->id ?>"/>
          <div>
            <input type="submit" value="Yes, delete"/>
          </div>
        </form>
      </div>
    </footer>

    <?php require_once __DIR__ . '/../components/error.php' ?>
  </main>

  <script>
    function copy_team_code() {
      const temp_textarea = document.createElement("textarea");
      temp_textarea.value = "<?= $team->id ?>";
      document.body.appendChild(temp_textarea);
      temp_textarea.focus();
      temp_textarea.select();
      document.execCommand("copy");
      document.body.removeChild(temp_textarea);

      const copy_button = document.getElementById("copy_button");
      const [symbol, text] = copy_button.children;
      console.log(symbol);
      console.log(text);
      symbol.textContent = "check";
      text.textContent = "Copied";
      setTimeout(() => {
        symbol.textContent = "content_copy";
        text.textContent = "Copy Code"
      }, 1500);
    }
  </script>
</body>
</html>