<?php
require_once __DIR__ . '/../api/Session.php';

$name = $password = null;
$is_registering = true;

if ($auth_form = Session::get('auth_form')) {
  $name = htmlspecialchars($auth_form['name']);
  $password = htmlspecialchars($auth_form['password']);
  $is_registering = $auth_form['action'] === 'register';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Authentication</title>

  <link rel="stylesheet" href="../css/base.css"/>
  <link rel="stylesheet" href="../css/auth.css"/>
</head>
<body>
  <aside>
    <h1>
      <i class="symbol">inventory</i>
      AppName
    </h1>
    <a href="https://github.com/m1841/db-final-project" target="_blank">
      © Mihai Mureșan 2024
    </a>
  </aside>
  <main id="app">
    <h1>
      <i class="symbol">inventory</i>
      AppName
    </h1>
    <h2>
      <?= $is_registering ? 'Welcome!' : 'Welcome Back!' ?>
    </h2>
    <form method="POST" action="../api/User.php">
      <input type="hidden" name="action" id="action" required
        value="<?= $is_registering ? 'register' : 'login' ?>"/>
      <label for="name">
        <input type="text" name="name" id="name" placeholder="Username"
          required minlength="3" maxlength="32" value="<?= $name ?>"/>
      </label>

      <div>
        <label for="password">
          <input type="password" name="password" id="password"
            placeholder="Password" required minlength="8"
            maxlength="32" value="<?= $password ?>"/>
        </label>
        <button onclick="toggle_password_visibility()" type="button"
          class="symbol" id="password_visibility_toggle">
          visibility_off
        </button>
      </div>

      <input type="submit"
        value="<?= $is_registering ? 'Create account' : 'Log in' ?>"/>
    </form>
    <button onclick="toggle_action()" type="button" id="action_toggle">
      <?= $is_registering ? 'Already have an account?' : 'Don\'t have an account?' ?>
    </button>

    <?php if (Session::get('error') !== null) { ?>
      <p><?= Session::get('error') ?></p>
      <?php
      Session::unset('error');
    }
    ?>
  </main>
  <script>
    function toggle_action() {
      let action = document.getElementById("action");
      let submit_button = document.querySelector("input[type=submit]");
      let toggle_button = document.getElementById("action_toggle")
      let title = document.querySelector("h2");

      let is_registering = action.value === "register";

      action.value = is_registering ? "login" : "register";
      submit_button.value = is_registering ? "Log in" : "Create account";
      toggle_button.textContent = is_registering
        ? "Don't have an account?" : "Already have an account?";
      title.textContent = is_registering ? "Welcome back!" : "Welcome!";
    }

    function toggle_password_visibility() {
      let password_input = document.getElementById("password");
      let visibility_button = document.getElementById("password_visibility_toggle")

      let is_password_visible = password_input.type === "text";

      password_input.type = is_password_visible ? "password" : "text";
      visibility_button.textContent = is_password_visible
        ? "visibility_off" : "visibility";
    }
  </script>
</body>
</html>
