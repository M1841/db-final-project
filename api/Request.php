<?php

abstract class Request
{
  public static function method(): string
  {
    return $_SERVER['REQUEST_METHOD'];
  }

  public static function post(string $key): string|null
  {
    return isset($_POST[$key]) ? htmlspecialchars(trim($_POST[$key])) : null;
  }

  public static function get(string $key): string|null
  {
    return isset($_GET[$key]) ? htmlspecialchars(trim($_GET[$key])) : null;
  }

  public static function get_array(string $array): array|null
  {
    if (isset($_GET[$array])) {
      $result = [];
      foreach ($_GET[$array] as $element) {
        $result[] = htmlspecialchars(trim($element));
      }
      return $result;
    } else {
      return null;
    }
  }
}