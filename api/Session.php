<?php

readonly class Session
{
  public static function init(): void
  {
    session_set_cookie_params([
      'lifetime' => PHP_INT_MAX,
      'path' => '/',
      'domain' => '',
      'secure' => false,
      'httponly' => true,
      'samesite' => 'Lax'
    ]);
    session_start();
  }

  public static function set(string $key, mixed $value): void
  {
    $_SESSION[$key] = $value;
  }

  public static function get(?string $key = NULL)
  {
    if (!isset($key)) {
      return $_SESSION;
    } else {
      if (!isset($_SESSION[$key])) {
        return NULL;
      } else {
        return $_SESSION[$key];
      }
    }
  }

  public static function unset(?string $key = NULL): void
  {
    if (!isset($key)) {
      session_unset();
    } else {
      unset($_SESSION[$key]);
    }
  }
}

Session::init();
