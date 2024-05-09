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

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap"
    rel="stylesheet">
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>

  <script src="./lib/flowbite.min.js"></script>
  <script src="./lib/tailwind.min.js"></script>
</head>
<body class="h-screen selection:bg-zinc-200" style="font-family: Inter, sans-serif">
  <nav class="py-2 px-4 border-b-[1px] border-b-zinc-200 flex justify-between
  items-center relative gap-4">
    <div class="flex gap-4 sm:gap-6">
      <a href="./"
        class="text-sm font-medium hover:underline outline-none
        focus-visible:underline">Overview</a>
      <a href="./teams"
        class="text-sm font-medium text-zinc-500 hover:underline outline-none
        focus-visible:underline">Teams</a>
      <a href="./projects"
        class="text-sm font-medium text-zinc-500 hover:underline outline-none
        focus-visible:underline">Projects</a>
      <a href="./tasks"
        class="text-sm font-medium text-zinc-500 hover:underline outline-none
        focus-visible:underline">Tasks</a>
    </div>
    <button id="dropdown_button" data-dropdown-toggle="account_dropdown"
      class="bg-transparent py-2 pr-3 pl-11 relative flex justify-between items-center
      focus:outline-zinc-100 focus:outline-4 border-[1px] border-zinc-200 p-2
      rounded-md text-sm outline-none outline-offset-0 w-48
      focus-visible:outline-zinc-100 focus-visible:outline-4 hover:bg-zinc-100">
      <span class="bg-zinc-300 text-zinc-500 rounded-full p-1 aspect-square
      font-medium text-xs flex justify-center items-center absolute left-3">
        <span class="h-4 w-4">
          <?= mb_strtoupper($user->name[0]) ?>
        </span>
      </span>
      <?= $user->name ?>
      <span class="material-symbols-outlined text-zinc-500 font-light text-sm">
        unfold_more
      </span>
    </button>
    <div id="account_dropdown"
      class="absolute hidden bg-zinc-50 border-[1px] border-zinc-200 rounded-md
      p-1 w-full sm:w-48">
      <form method="POST" action="./api/User.php">
        <input type="hidden" name="auth_type" required value="logout"/>
        <input type="submit" value="Logout"
          class="text-sm cursor-pointer hover:bg-zinc-100 outline-none
        focus-visible:bg-zinc-100 w-full text-start px-4 py-1.5 rounded-md"/>
      </form>
    </div>
  </nav>

  <main class="p-4">
    <h2 class="text-lg font-medium">
      Your Teams
    </h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">
      <?php
      foreach ($user->get_teams() as $team) {
        echo '
        <div class="flex border-[1px] border-zinc-200 p-4 rounded-md">
          ' . $team->name . '
        </div>
      ';
      }
      ?>
    </div>
  </main>
</body>
</html>