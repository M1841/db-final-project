<?php

abstract class Request
{
  public static function method(): string
  {
    return $_SERVER['REQUEST_METHOD'];
  }

  public static function post(string $key): string|null
  {
    return isset($_POST[$key]) ? $_POST[$key] : null;
  }

  public static function get(string $key): string|null
  {
    return isset($_GET[$key]) ? $_GET[$key] : null;
  }
}