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
      SessionManager::startSession();
      $sessionId = session_id();

      // コードマスタデータの取得
      $codeMasterModel = new CodeMasterModel();
      $codeMasterData = $codeMasterModel->getAllCodeMasterData();

      // カテゴリデータの取得
      $majorCategoryModel = new MajorCategoryModel();
      $majorCategories = $majorCategoryModel->getAllMajorCategories();
      $minorCategoryModel = new MinorCategoryModel();
      $minorCategories = $minorCategoryModel->getAllMinorCategories();

      // 色データの取得
      $colorModel = new ColorModel();
      $colors = $colorModel->getAllColors();

      // レスポンスとしてユーザーID、セッションID、コードマスターデータを返す
      Response::sendJSON([
        'userId' => $user['user_id'],
        'sessionId' => $sessionId,
        'codeMaster' => $codeMasterData,
        'major_category' => $majorCategories,
        'minor_category' => $minorCategories,
        'colors' => $colors
      ]);
    } else {
      // 認証失敗
      Response::sendError(401);
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
      Response::sendError(500);
    }
  }
}
