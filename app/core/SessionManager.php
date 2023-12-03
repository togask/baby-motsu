<?php
class SessionManager
{
  public static function startSession()
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }

  public static function checkSessionId()
  {
    return isset($_SESSION['user_id']);
  }

  public static function set($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  public static function get($key)
  {
    return $_SESSION[$key] ?? null;
  }

  public static function remove($key)
  {
    unset($_SESSION[$key]);
  }

  public static function destroySession()
  {
    session_unset();
    session_destroy();
  }

  public static function regenerateSessionId($deleteOldSession = false)
  {
    session_regenerate_id($deleteOldSession);
  }
}
