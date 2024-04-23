<?php
require_once 'Session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>db_final_project</title>
</head>
<body>
  <?php
  if (Session::get('account') === null) {
    header('Location: ./auth');
    exit();
  } else {
    ?>
      <h1>Welcome <?=Session::get('account')['name']?>!</h1>
      <form method="POST" action="Account.php">
      <input type="hidden" name="auth_type" required value="logout"/>
        <input type="submit" value="Logout"/>
      </form>
    <?php
  }
  ?>
</body>
</html>