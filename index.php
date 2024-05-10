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
<body class="h-screen selection:bg-zinc-200 bg-zinc-100" style="font-family: Inter,
sans-serif">
  <nav class="py-2 px-4 border-b-[1px] border-b-zinc-200 flex justify-between
  items-center relative gap-4 bg-zinc-50 drop-shadow-sm">

    <div class="flex gap-6 items-center">
      <h1 class="font-semibold flex gap-1">
        <span class="material-symbols-outlined">
          inventory
        </span>
        App Title
      </h1>
      <a href="./teams"
        class="text-sm font-medium text-zinc-500 hover:text-zinc-900 outline-none
        focus-visible:text-zinc-900 transition-all duration-100">Teams</a>
      <a href="./projects"
        class="text-sm font-medium text-zinc-500 hover:text-zinc-900 outline-none
        focus-visible:text-zinc-900 transition-all duration-100">Projects</a>
      <a href="./tasks"
        class="text-sm font-medium text-zinc-500 hover:text-zinc-900 outline-none
        focus-visible:text-zinc-900 transition-all duration-100">Tasks</a>
    </div>
    <button id="dropdown_button" data-dropdown-toggle="account_dropdown"
      class="bg-transparent py-2 pr-3 pl-11 relative flex justify-between items-center
      focus:outline-zinc-100 focus:outline-4 border-[1px] border-zinc-200 p-2
      rounded-md text-sm outline-none outline-offset-0 w-48
      focus-visible:outline-zinc-100 focus-visible:outline-4 hover:bg-zinc-100
      drop-shadow-sm transition-all duration-100">
      <span class="bg-zinc-300 text-zinc-500 rounded-full aspect-square
      font-medium text-xs flex justify-center items-center absolute left-3">
        <span class="h-6 w-6 flex justify-center items-center">
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
      p-1 w-full sm:w-48 drop-shadow-sm">
      <form method="POST" action="./api/User.php">
        <input type="hidden" name="auth_type" required value="logout"/>
        <input type="submit" value="Logout"
          class="text-sm cursor-pointer hover:bg-zinc-100 outline-none
        focus-visible:bg-zinc-100 w-full text-start px-4 py-1.5 rounded-md transition-all duration-100"/>
      </form>
    </div>
  </nav>

  <main class="p-4 lg:py-8 lg:px-16">
    <div class="flex flex-col gap-2">
      <h2 class="font-semibold">
        Teams
      </h2>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 lg:gap-8">
        <?php
        foreach ($user->get_teams() as $team) {
          echo '
          <div class="bg-zinc-50 border-[1px] border-zinc-200 rounded-md p-4 drop-shadow-sm flex flex-col">
              <div class="flex justify-between items-center">
                <h3 class="text-sm flex items-center gap-1">
                  <span class="material-symbols-outlined text-xl">
                    group
                  </span>
                  ' . $team->name . '
                </h3>
                <p class="text-xs text-zinc-500">
                  ' . count($team->members) . (count($team->members) > 1
              ? ' Members'
              : ' Member') . '
                </p>
              </div>
              <div class="flex flex-col">';

          foreach ($team->members as $member) {
            echo '
              <div class="flex">
                <span class="bg-zinc-300 text-zinc-500 rounded-full aspect-square
                  font-medium text-xs flex justify-center items-center">
                  <span class="h-6 w-6 flex justify-center items-center">
                    ' . mb_strtoupper($member->name[0]) . '
                  </span>
                </span>
                ' . mb_strtoupper($member->name) . '
              </div>
            ';
          }

          echo '
              </div>
          </div>  
        ';
        }
        ?>
      </div>
    </div>
  </main>
</body>
</html>