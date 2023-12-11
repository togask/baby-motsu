<?php
class SessionManager
{
  private static $db;

  public static function initialize(Database $database)
  {
    self::$db = $database;
  }

  public static function startSession()
  {
    if (session_status() == PHP_SESSION_NONE) {
      session_start();
    }
  }

  public static function checkSessionId($userId, $sessionId)
  {
    // データベースからユーザーのセッション情報を取得
    $stmt = self::$db->prepare("SELECT session_id FROM USER WHERE user_id = :userId");
    self::$db->execute($stmt, ['userId' => $userId]);
    $userSession = self::$db->fetch($stmt);

    // ユーザーのセッションIDが存在し、リクエストされたセッションIDと一致するか確認
    return $userSession && $userSession['session_id'] == $sessionId;
  }

  public static function set($key, $value)
  {
    $_SESSION[$key] = $value;
  }

  public static function get($key, $default = null)
  {
    if (!is_string($key)) {
      throw new InvalidArgumentException("Invalid session key.");
    }

    return $_SESSION[$key] ?? $default;
  }


  public static function remove($key)
  {
    if (!isset($_SESSION[$key])) {
      // オプション: 例外をスローするか、ログに記録する
      throw new InvalidArgumentException("Session key not found.");
    }

    unset($_SESSION[$key]);
  }


  public static function destroySession()
  {
    if (session_status() === PHP_SESSION_ACTIVE) {
      session_unset();
      session_destroy();
    }
  }
}
