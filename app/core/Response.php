<?php
class Response
{
  public static function sendResponse($statusCode, $data = null)
  {
    header("HTTP/1.1 " . $statusCode . " " . self::getStatusCodeMessage($statusCode));

    if ($statusCode == 200) {
      // HTTPステータスコードが200の場合、JSONデータを送信
      header('Content-Type: application/json');
      echo json_encode($data);
    } elseif ($statusCode >= 400 && $statusCode < 500) { // HTTPステータスコードが400台の場合、エラーメッセージを送信
      header('Content-Type: application/json');
      echo json_encode(['error' => self::getStatusCodeMessage($statusCode)]);
    }

    exit;
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
    return (isset($statusCodes[$code])) ? $statusCodes[$code] : 'Error';
  }
}
