<?php
class AuthController
{
  /**
   * ログイン処理を行う
   */
  public function login()
  {
    // リクエストからJSONデータを取得
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    // ユーザーの認証を行う
    $user = $this->authenticateUser($email, $password);

    if ($user) {
      // 認証成功
      // セッションIDを生成または取得
      SessionManager::startSession();
      $sessionId = session_id();

      // レスポンスとしてユーザーIDとセッションIDを返す
      Response::sendResponse(200, [
        'userId' => $user->getId(),
        'sessionId' => $sessionId
      ]);
    } else {
      // 認証失敗
      Response::sendResponse(401);
    }
  }

  /**
   * ユーザーを認証する
   * @param string $email メールアドレス
   * @param string $password パスワード
   * @return mixed 認証されたユーザーオブジェクトまたはfalse
   */
  private function authenticateUser($email, $password)
  {
    try {
      // UserModelを使用してユーザーを検索
      $userModel = new UserModel();
      $user =  $userModel->findByEmailAndPassword($email, $password);
      return $user;
    } catch (\PDOException $e) {
      // データベースエラーのハンドリング
      Response::sendResponse(500);
    }
  }
}
