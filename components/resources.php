<main>
  <section>
    <h2>Teams</h2>
    <div>
      <?php if ($teams) {
        foreach ($teams as $team) {
          $member_count = count($team->get_members());
          $project_count = count($team->get_projects()); ?>
          <a href="./team?id=<?= $team->id ?>">
            <div>
              <h3><?= $team->name ?></h3>
              <span>
                  <?= $member_count ?>
                  Member<?= $member_count == 1 ? '' : 's' ?>
                </span>
            </div>
            <div>
              <p><?= $team->description ?></p>
              <p>
                <?= $project_count ?>
                Project<?= $project_count == 1 ? '' : 's' ?>
              </p>
            </div>
          </a>
        <?php }
      } else { ?>
        <span>No teams found</span>
      <?php } ?>
    </div>
  </section>

  <section>
    <h2>Projects</h2>
    <div>
      <?php if ($projects) {
        foreach ($projects as $project) {
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
        <?php }
      } else { ?>
        <span>No projects found</span>
      <?php } ?>
    </div>
  </section>

  <section>
    <h2>Tasks</h2>
    <div>
      <?php if ($tasks) {
        foreach ($tasks as $task) { ?>
          <a href="./task?id=<?= $task->id ?>">
            <div>
              <h3><?= $task->name ?></h3>
              <span>
                <?= $task->priority->value ?> Priority
              </span>
            </div>
            <div>
              <p><?= $task->description ?></p>
              <p><?= $task->status->value ?></p>
            </div>
          </a>
        <?php }
      } else { ?>
        <span>No tasks found</span>
      <?php } ?>
    </div>
  </section>
  <?php
  if (Session::get('error') !== null) { ?>
    <div>
      <p><?= Session::get('error') ?></p>
    </div>
    <?php
    Session::unset('error');
  }
  ?>
</main>
