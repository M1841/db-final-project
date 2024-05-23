<header>
  <nav>
    <h1>
      <a href="./">
        <i class="symbol">inventory</i>
        AppName
      </a>
    </h1>

    <a href="./teams">Teams</a>
    <a href="./projects">Projects</a>
    <a href="./tasks">Tasks</a>
  </nav>

  <form method="GET" action="./">
    <label for="search">
      <input type="text" id="search" name="search"
        placeholder="Search All" value="<?= $query ?>"/>
    </label>
    <input type="submit" class="symbol" value="search"/>
  </form>

  <button data-dropdown-toggle="account_dropdown">
    <div>
      <span><?= mb_strtoupper($user->name[0]) ?></span>
    </div>
    <?= $user->name ?>

    <i class="symbol">unfold_more</i>
  </button>

  <div id="account_dropdown" class="hidden">
    <form method="POST" action="../api/User.php">
      <input type="hidden" name="action" required value="logout"/>
      <input type="submit" value="Logout"/>
    </form>
  </div>
</header>