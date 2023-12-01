<?php
class ErrorHandler
{
  public static function handle400Error()
  {
    header("HTTP/1.1 400 Bad Request");
    echo json_encode(['error' => 'Bad request']);
    exit;
  }

  public static function handle401Error()
  {
    header("HTTP/1.1 401 Unauthorized");
    echo json_encode(['error' => 'Unauthorized']);
    exit;
  }

  public static function handle404Error()
  {
    header("HTTP/1.1 404 Not Found");
    echo json_encode(['error' => 'Not Found']);
    exit;
  }
}
