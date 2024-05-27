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
$tasks = $user->get_tasks();
$query = Request::get('search');
if ($query) {
  $statuses = Request::get_array('status');
  $priorities = Request::get_array('priority');
  $tasks = Task::search($query, $statuses ?? [
    TaskStatus::NotStarted->value,
    TaskStatus::InProgress->value,
    TaskStatus::Completed->value
  ], $priorities ?? [
    TaskPriority::Low->value,
    TaskPriority::High->value
  ],
    !Request::get('name'), !Request::get('description'));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Tasks</title>

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
      <h2>Your Tasks</h2>
      <?php require_once __DIR__ . '/../components/tasks.php' ?>
    </section>
    <footer>
      <button data-modal-target="search_modal" data-modal-toggle="search_modal">
        <i class="symbol">search</i>
        Search Tasks
      </button>
      <button data-modal-target="create_modal" data-modal-toggle="create_modal">
        <i class="symbol">add</i>
        Create a Task
      </button>

      <div id="search_modal" tabindex="-1" class="hidden">
        <form method="GET" action="./tasks">
          <h3>Search Tasks</h3>
          <label for="task_query">
            <input type="text" name="search" id="task_query"
              placeholder="Search Tasks" value="<?= $query ?>"/>
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
          <fieldset>
            <legend>Status</legend>
            <?php foreach (TaskStatus::cases() as $case) { ?>
              <label for="<?= $case->value ?>">
                <input type="checkbox" name="status[]"
                  id="<?= $case->value ?>" value="<?= $case->value ?>"
                  <?= in_array($case->value, $statuses ?? TaskStatus::cases())
                    ? 'checked' : '' ?>/>
                <?= $case->value ?>
              </label>
            <?php } ?>
          </fieldset>
          <fieldset>
            <legend>Priority</legend>
            <?php foreach (TaskPriority::cases() as $case) { ?>
              <label for="<?= $case->value ?>">
                <input type="checkbox" name="priority[]"
                  id="<?= $case->value ?>" value="<?= $case->value ?>"
                  <?= in_array($case->value, $priorities ?? TaskPriority::cases())
                    ? 'checked' : '' ?>/>
                <?= $case->value ?>
              </label>
            <?php } ?>
          </fieldset>
          <div>
            <input type="submit" value="Search"/>
            <a href="./tasks">Clear</a>
          </div>
        </form>
      </div>
      <div id="create_modal" tabindex="-1" class="hidden">
        <form method="POST" action="../api/Task.php">
          <h3>Create a Task</h3>
          <input type="hidden" name="resource" value="task"/>
          <input type="hidden" name="action" value="create"/>
          <input type="hidden" name="user_id" value="<?= $user->id ?>"/>
          <label for="task_project_id">
            <select name="project_id" required id="task_project_id">
              <option value="">Choose a Project</option>
              <?php foreach ($projects as $project) { ?>
                <option value="<?= $project->id ?>">
                  <?= $project->name ?>
                </option>
              <?php } ?>
            </select>
          </label>
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
    </footer>
    <?php require_once __DIR__ . '/../components/error.php' ?>
</body>