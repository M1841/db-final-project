<div>
  <?php if ($teams) {
    foreach ($teams as $team) {
      $member_count = count($team->get_members());
      $project_count = count($team->get_projects()); ?>
      <a href="./team?id=<?= $team->id ?>">
        <div>
          <h3>
            <i class="symbol">group</i>
            <?= $team->name ?>
          </h3>
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