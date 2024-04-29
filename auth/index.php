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

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link
    href="https://fonts.googleapis.com/css2?family=Inter:wght@100..900&display=swap"
    rel="stylesheet">

  <script src="../lib/vue.min.js"></script>
  <script src="../lib/tailwind.min.js"></script>
  <!--  <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.4.26/vue.global.min.js"></script>-->
</head>
<body class="h-screen" style="font-family: Inter, sans-serif">
  <main id="app" class="h-full w-full flex">
    <div class="hidden lg:flex h-full w-1/2 bg-zinc-900 flex-col justify-between
    text-zinc-50
     p-8">
      <h1 class="font-medium">App Title</h1>
    </div>
    <div class="h-full w-full lg:w-1/2 flex flex-col justify-center items-center
    gap-4">
      <h1 class="text-2xl font-semibold">
        {{auth_type === "login" ? "Welcome back" : "Create an account"}}
      </h1>
      <form method="POST" action="../api/User.php" class="flex flex-col
       gap-2 w-2/3 sm:w-1/2">
        <input type="hidden" name="auth_type" id="auth_type" required
          v-model="auth_type"/>

        <label for="name" class="flex flex-col">
          <input type="text" name="name" id="name" placeholder="Username"
            required minlength="3" maxlength="32" v-model="name"
            class="border-[1px] border-zinc-200 p-2 rounded-md text-sm w-full
            outline-none outline-offset-0 focus-visible:outline-zinc-400
            focus-visible:outline-[1px] placeholder:text-zinc-500"
          />
        </label>

        <label for="password">
          <input type="password" name="password" id="password" placeholder="Password"
            required minlength="8" maxlength="32" v-model="password"
            class="border-[1px] border-zinc-200 p-2 rounded-md text-sm w-full
            outline-none outline-offset-0 focus-visible:outline-zinc-400
            focus-visible:outline-[1px] placeholder:text-zinc-500"
          />
        </label>

        <input type="submit" value="Submit"
          class="bg-zinc-900 text-zinc-50 p-2 rounded-md w-full mt-2 text-sm
          cursor-pointer outline-none outline-offset-0 focus-visible:outline-zinc-400
            focus-visible:outline-[1px]"/>
      </form>
      <button @click="toggleAuthType"
        class="hover:underline text-sm text-zinc-500 outline-none
        focus-visible:underline">
        {{ auth_type === "login"
          ? "Don't have an account?"
          : "Already have an account?" }}
      </button>

      <p v-text="error"></p>
    </div>
  </main>

  <script>
    window.onload = function () {
      const app = Vue.createApp({
        data() {
          return {
            name: "<?= $name ?>",
            password: "<?= $password ?>",
            auth_type: '<?= $is_registering ? "register" : "login" ?>',
            error: "<?= Session::get('error') ?>"
          };
        },
        methods: {
          toggleAuthType() {
            this.auth_type = this.auth_type === "login" ? "register" : "login";
          }
        }
      });
      app.mount("#app");
    }
  </script>
</body>
</html>
