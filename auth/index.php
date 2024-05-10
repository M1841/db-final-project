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
  <link rel="stylesheet"
    href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200"/>

  <script src="../lib/tailwind.min.js"></script>
  <script src="../lib/vue.min.js"></script>
  <!--  <script src="https://cdnjs.cloudflare.com/ajax/libs/vue/3.4.26/vue.global.min.js"></script>-->
</head>
<body class="h-screen selection:bg-zinc-200" style="font-family: Inter, sans-serif">
  <main id="app" class="h-full w-full flex">
    <div class="hidden lg:flex h-full w-1/2 bg-zinc-900 flex-col justify-between
    text-zinc-50 p-8" style="
      background: radial-gradient(circle at bottom left ,#09090b 35%, transparent 36%), radial-gradient(circle at top right ,#09090b 35%, transparent 36%);
        background-size: 4.45rem 4.45rem;
        background-color: #18181b;
        opacity: 1
      ">
      <h1 class="font-medium flex gap-1">
        <span class="material-symbols-outlined">
          inventory
        </span>
        App Title
      </h1>
    </div>
    <div class="h-full w-full lg:w-1/2 flex flex-col justify-center items-center
    gap-4 relative">
      <h1 class="font-medium flex gap-1 absolute top-8 lg:hidden">
        <span class="material-symbols-outlined">
          inventory
        </span>
        App Title
      </h1>
      <h2 class="text-2xl font-semibold">
        {{auth_type === "login" ? "Welcome back!" : "Welcome!"}}
      </h2>
      <form method="POST" action="../api/User.php" class="flex flex-col
       gap-2 w-2/3 sm:w-1/2">
        <input type="hidden" name="auth_type" id="auth_type" required
          v-model="auth_type" :value="auth_type"/>

        <label for="name">
          <input type="text" name="name" id="name" placeholder="Username"
            required minlength="3" maxlength="32" v-model="name"
            class="border-[1px] border-zinc-200 p-2 rounded-md text-sm w-full
            outline-none outline-offset-0 focus-visible:outline-zinc-100
            focus-visible:outline-4 hover:bg-zinc-100
            placeholder:text-zinc-500 transition-all duration-100"
          />
        </label>

        <div class="flex w-full">
          <label for="password" class="w-full">
            <input :type="[is_password_visible ? 'text' : 'password']" name="password"
              id="password"
              placeholder="Password"
              required minlength="8" maxlength="32" v-model="password"
              class="border-[1px] border-zinc-200 p-2 rounded-l-md text-sm w-full
            outline-none outline-offset-0 focus-visible:outline-zinc-100
            focus-visible:outline-4 hover:bg-zinc-100
            placeholder:text-zinc-500 transition-all duration-100"
            />
          </label>
          <button @click="togglePasswordVisibility" type="button"
            class="material-symbols-outlined text-xl font-light
            text-zinc-400 border-[1px] border-zinc-200 border-l-transparent px-2
            rounded-r-md outline-none outline-offset-0 focus-visible:outline-zinc-100
            focus-visible:outline-4 hover:bg-zinc-100 transition-all duration-100"
            v-html="[is_password_visible
             ? 'visibility' : 'visibility_off']">
          </button>
        </div>

        <input type="submit" :value="
          auth_type === 'login'
            ? 'Log in'
            : 'Create account'
         "
          class="bg-zinc-900 text-zinc-50 p-2 rounded-md mt-2 text-sm
          cursor-pointer outline-none outline-offset-0
          focus-visible:outline-zinc-200 hover:outline-zinc-200 hover:outline-4
            focus-visible:outline-4 active:bg-zinc-700 transition-all duration-100"/>
      </form>
      <button @click="toggleAuthType" type="button"
        class="hover:text-zinc-900 text-sm text-zinc-400 outline-none
        focus-visible:text-zinc-900 transition-all duration-100">
        {{ auth_type === "login"
          ? "Don't have an account?"
          : "Already have an account?" }}
      </button>
      <p v-if="error !== ''" v-text="error"
        class="absolute bottom-8 rounded-md p-2 text-center text-sm
      bg-rose-200/50 text-rose-500/75 w-2/3 sm:w-1/2"></p>
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
            error: "<?= Session::get('error') ?>",
            is_password_visible: false
          };
        },
        methods: {
          toggleAuthType() {
            this.auth_type = this.auth_type === "login" ? "register" : "login";
          },
          togglePasswordVisibility() {
            this.is_password_visible = !this.is_password_visible;
          }
        }
      });
      app.mount("#app");
    }
  </script>
</body>
</html>
