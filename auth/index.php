<?php
require_once __DIR__ . '/../api/Session.php';

$name = $password = $is_registering = NULL;

if ($auth_form = Session::get('auth_form')) {
  $name = htmlspecialchars($auth_form['name']);
  $password = htmlspecialchars($auth_form['password']);
  $is_registering = $auth_form['auth_type'] === 'register';
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
      <i>inventory</i>
      App Title
    </h1>
  </aside>

  <main id="app">
    <h1>
      <i class="symbol">inventory</i>
      App Title
    </h1>
    <h2>
      Welcome!
    </h2>
    <form method="POST" action="../api/User.php">
      <input type="hidden" name="auth_type" id="auth_type" required
        value="register"/>

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

      <input type="submit" value="Create account"/>
    </form>
    <button onclick="toggle_auth_type()" type="button" id="auth_type_toggle">
      Already have an account?
    </button>

    <?php if (Session::get('error') !== null) { ?>
      <p><?= Session::get('error') ?></p>
    <?php } ?>
  </main>

  <script>
    function toggle_auth_type() {
      let auth_type = document.getElementById("auth_type");
      let submit_button = document.querySelector("input[type=submit]");
      let toggle_button = document.getElementById("auth_type_toggle")
      let title = document.querySelector("h2");

      let is_registering = auth_type.value === "register";

      auth_type.value = is_registering ? "login" : "register";
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

    <?php if ($is_registering) { ?>
    toggle_auth_type();
    <?php } ?>
  </script>
</body>
</html>
