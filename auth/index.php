<?php
require_once '../Session.php';

$name = htmlspecialchars(Session::get('auth_form')['name']);
$password = htmlspecialchars(Session::get('auth_form')['password']);
$is_registering = Session::get('auth_form')['auth_type'] === 'register';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">

  <title>Auth</title>
</head>
<body>
  <form method="POST" action="../Account.php">
    <input type="hidden" name="auth_type" required value="login" id="authTypeInput"/>

    <label for="name">
      Name
      <input type="text" name="name" required
        <?=$name ? 'value="'.$name.'"' : ''?>
      />
    </label>

    <label for="password">
      Password
      <input type="password" name="password" required
        <?=$password ? 'value="' . $password . '"' : ''?>
      />
    </label>

    <input type="submit" value="Login" id="submitButton"/>
  </form>
    
  <button onclick="toggleAuthType()" id="authTypeToggle">
    Don't have an account? Register one
  </button>

  <?=Session::get('error')?>

  <script>
    function toggleAuthType() {
      let authTypeInput = document.getElementById("authTypeInput");
      let submitButton = document.getElementById("submitButton");
      let authTypeToggle = document.getElementById("authTypeToggle");

      let isAuthTypeRegister = authTypeInput.value === "register";

      authTypeInput.value = isAuthTypeRegister ? "login" : "register";
      submitButton.value = isAuthTypeRegister ? "Login" : "Register";
      authTypeToggle.innerHTML = isAuthTypeRegister
        ? "Don't have an account? Register one"
        : "Already have an account? Login instead"
    }

    <?php if($is_registering) { echo 'toggleAuthType()'; }?>
  </script>
</body>
</html>