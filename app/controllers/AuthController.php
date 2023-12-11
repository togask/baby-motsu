<?php
class AuthController
{
  private $db;

  public function __construct(Database $db)
  {
    $this->db = $db;
  }

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
      SessionManager::startSession($user['user_id']);
      $sessionId = session_id();

      // コードマスタデータの取得
      $codeMasterModel = new CodeMasterModel($this->db);
      $codeMasterData = $codeMasterModel->getAllCodeMasterData();

      // カテゴリデータの取得
      $majorCategoryModel = new MajorCategoryModel($this->db);
      $majorCategories = $majorCategoryModel->getAllMajorCategories();
      $minorCategoryModel = new MinorCategoryModel($this->db);
      $minorCategories = $minorCategoryModel->getAllMinorCategories();

      // 色データの取得
      $colorModel = new ColorModel($this->db);
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
      $userModel = new UserModel($this->db);
      $user =  $userModel->findByEmailAndPassword($email, $password);
      return $user;
    } catch (\PDOException $e) {
      // データベースエラーのハンドリング
      Response::sendError(500);
    }
  }
}
