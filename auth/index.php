<?php
require_once '../Session.php';
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
  <?php
  //  unset($_SESSION);
  echo '
    <form method="POST" action="../Account.php">
      <input type="hidden" name="auth_type" required value="register" id="authTypeInput"/>
      <input type="text" name="name" required placeholder="Name"/>
      <input type="password" name="password" placeholder="Password" required/>
      <input type="submit" value="Register" id="submitButton"/>
    </form>
  ';
  echo Session::get('error');
  ?>

  <button onclick="toggleAuthType()" id="authTypeToggle">
    Already have an account? Login instead
  </button>

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
  </script>
</body>
</html>