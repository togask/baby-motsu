<?php
class Response
{
  public static function sendJSON($data, $statusCode = 200)
  {
    header('Content-Type: application/json');
    header("HTTP/1.1 " . $statusCode . " " . self::getStatusCodeMessage($statusCode));
    echo json_encode($data);
    exit;
  }

  public static function sendError($errorCode, $errorMessage = null)
  {
    self::sendJSON(['error' => $errorMessage ?: self::getStatusCodeMessage($errorCode)], $errorCode);
  }

  private static function getStatusCodeMessage($code)
  {
    $statusCodes = [
      200 => 'OK',
      201 => 'Created',
      204 => 'No Content',
      400 => 'Bad Request',
      401 => 'Unauthorized',
      404 => 'Not Found',
      500 => 'Internal Server Error'
    ];
    return $statusCodes[$code] ?? 'Unknown Status';
  }
}
