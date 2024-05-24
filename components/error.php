<?php
if (Session::get('error') !== null) { ?>
  <footer>
    <p><?= Session::get('error') ?></p>
  </footer>
  <?php
  Session::unset('error');
}
?>