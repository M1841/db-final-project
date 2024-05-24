<div>
  <?php if ($projects) {
    foreach ($projects as $project) {
      $task_count = count($project->get_tasks()); ?>
      <a href="./project?id=<?= $project->id ?>">
        <div>
          <h3>
            <i class="symbol">design_services</i>
            <?= $project->name ?>
          </h3>
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