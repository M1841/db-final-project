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
    header('Location: ../projects');
    exit();
  }
  $project = Project::get($id);
  if ($project === null) {
    throw new Exception('Could not find project');
  }
  $team = $project->team;
  if (!$user->is_in_team($team)) {
    throw new Exception('You cannot view a project from a team you are not in');
  }
  $members = $project->team->get_members();
  $tasks = $project->get_tasks();
} catch (Exception $err) {
  Session::set('error', $err->getMessage());
  header('Location: ../projects');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title><?= $project->name ?></title>

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
        <i class="symbol">design_services</i>
        <?= $project->name ?>
      </h1>
      <p><?= $project->description ?></p>
      <span>Project lead: <?= $project->lead->name ?></span>
    </header>
    <section>
      <h2>Tasks</h2>
      <?php require_once __DIR__ . '/../components/tasks.php' ?>
    </section>
    <footer>
      <button data-modal-target="edit_modal" data-modal-toggle="edit_modal">
        <i class="symbol">edit</i>
        Edit Project
      </button>
      <button data-modal-target="create_modal" data-modal-toggle="create_modal">
        <i class="symbol">add</i>
        Create a Task
      </button>
      <button data-modal-target="delete_modal"
        data-modal-toggle="delete_modal">
        <i class="symbol">delete</i>
        Delete Project
      </button>

      <div id="edit_modal" tabindex="-1" class="hidden">
        <form method="POST" action="../api/Project.php">
          <h3>Edit Project</h3>
          <input type="hidden" name="resource" value="project"/>
          <input type="hidden" name="action" value="edit"/>
          <input type="hidden" name="id" value="<?= $project->id ?>"/>
          <label for="project_name">
            <input type="text" name="name" placeholder="Project Name"
              required id="project_name" value="<?= $project->name ?>"/>
          </label>
          <label for="project_description">
            <textarea name="description" placeholder="Project Description"
              id="project_description"><?= $project->description ?></textarea>
          </label>
          <input type="submit" value="Edit"/>
        </form>
      </div>
      <div id="create_modal" tabindex="-1" class="hidden">
        <form method="POST" action="../api/Task.php">
          <h3>Create a Task</h3>
          <input type="hidden" name="resource" value="task"/>
          <input type="hidden" name="action" value="create"/>
          <input type="hidden" name="project_id" value="<?= $project->id ?>"/>
          <label for="task_status">
            <select name="status" required id="task_status">
              <option value="">Choose a Status</option>
              <?php foreach (TaskStatus::cases() as $case) { ?>
                <option value="<?= $case->value ?>">
                  <?= $case->value ?>
                </option>
              <?php } ?>
            </select>
          </label>
          <label for="task_priority">
            <select name="priority" required id="task_priority">
              <option value="">Choose a Priority</option>
              <?php foreach (TaskPriority::cases() as $case) { ?>
                <option value="<?= $case->value ?>">
                  <?= $case->value ?>
                </option>
              <?php } ?>
            </select>
          </label>
          <label for="task_user_id">
            <select name="user_id" required id="task_user_id">
              <option value="null">Unassigned</option>
              <?php foreach ($members as $member) { ?>
                <option value="<?= $member->id ?>">
                  <?= $member->name ?>
                </option>
              <?php } ?>
            </select>
          </label>
          <label for="task_name">
            <input type="text" name="name" placeholder="Task Name"
              required id="task_name"/>
          </label>
          <label for="task_description">
            <textarea name="description" placeholder="Task Description"
              id="task_description"></textarea>
          </label>
          <input type="submit" value="Create"/>
        </form>
      </div>
      <div id="delete_modal" tabindex="-1" class="hidden">
        <form method="POST" action="../api/Project.php">
          <h3>Are you sure?</h3>
          <input type="hidden" name="resource" value="project"/>
          <input type="hidden" name="action" value="delete"/>
          <input type="hidden" name="id" value="<?= $project->id ?>"/>
          <div>
            <input type="submit" value="Yes, delete"/>
          </div>
        </form>
      </div>
    </footer>
    <?php require_once __DIR__ . '/../components/error.php' ?>
  </main>
</body>