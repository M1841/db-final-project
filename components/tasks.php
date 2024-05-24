<div>
  <?php if ($tasks) {
    foreach ($tasks as $task) { ?>
      <a href="./task?id=<?= $task->id ?>">
        <div>
          <h3>
            <i class="symbol">task</i>
            <?= $task->name ?>
          </h3>
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