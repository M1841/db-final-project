<?php
require_once __DIR__ . '/api/User.php';
require_once __DIR__ . '/api/Session.php';

$user = Session::get('user');

if ($user === NULL) {
  header('Location: ./auth');
  exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Home</title>
</head>
<body>
  <h1>Welcome <?= $user->name ?>!</h1>
  <a href="/teams">Your Teams</a>
  <form method="POST" action="./api/User.php">
    <input type="hidden" name="auth_type" required value="logout"/>
    <input type="submit" value="Logout"/>
  </form>
</body>
</html>