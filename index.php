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

  <link rel="stylesheet" href="css/base.css"/>
  <link rel="stylesheet" href="css/index.css"/>

  <script src="./lib/tailwind.min.js"></script>
  <script src="./lib/flowbite.min.js"></script>
</head>

<body>
  <header>
    <nav>
      <h1>
        <i class="symbol">inventory</i>
        App Title
      </h1>

      <a href="./teams">Teams</a>
      <a href="./projects">Projects</a>
      <a href="./tasks">Tasks</a>
    </nav>

    <button data-dropdown-toggle="account_dropdown">
      <div>
        <span><?= mb_strtoupper($user->name[0]) ?></span>
      </div>
      <?= $user->name ?>

      <i class="symbol">unfold_more</i>
    </button>

    <div id="account_dropdown" class="hidden">
      <form method="POST" action="./api/User.php">
        <input type="hidden" name="auth_type" required value="logout"/>
        <input type="submit" value="Logout"/>
      </form>
    </div>
  </header>

  <main>
    <section>
      <h2>Teams</h2>
    </section>

    <section>
      <h2>Projects</h2>
    </section>

    <section>
      <h2>Tasks</h2>
    </section>
  </main>
</body>
</html>