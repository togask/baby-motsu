<?php
class AuthController
{
  private $db;
  private $codeMasterModel;
  private $majorCategoryModel;
  private $minorCategoryModel;
  private $colorModel;
  private $userModel;

  public function __construct(Database $db)
  {
    $this->db = $db;
    $this->codeMasterModel = new CodeMasterModel($this->db);
    $this->majorCategoryModel = new MajorCategoryModel($this->db);
    $this->minorCategoryModel = new MinorCategoryModel($this->db);
    $this->colorModel = new ColorModel($this->db);
    $this->userModel = new UserModel($this->db);
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
      $userId = $user['user_id'];
      // 認証成功
      SessionManager::startSession($userId);
      $sessionId = session_id();

      // その他のデータ取得とレスポンスの準備
      $this->prepareAndSendResponse($userId, $sessionId);
    } else {
      // 認証失敗
      Response::sendError(401);
    }
  }

  /**
   * ユーザー登録とログイン処理を行う
   */
  public function signup()
  {
    // リクエストからJSONデータを取得
    $data = json_decode(file_get_contents('php://input'), true);
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $nickname = $data['nickname'] ?? '';

    // 必須フィールドの確認
    if (!$email || !$password || !$nickname) {
      Response::sendError(400, 'Invalid input data');
      return;
    }

    // メールアドレスの形式の検証
    if (!$this->validateEmail($email)) {
      Response::sendError(400, 'Invalid email format');
      return;
    }

    // パスワードの検証
    if (!$this->validatePassword($password)) {
      Response::sendError(400, 'Invalid password format');
      return;
    }

    // パスワードをハッシュ化
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
      // ユーザーを登録
      $userId = $this->userModel->registerUser($email, $hashedPassword, $nickname);

      if ($userId) {
        // 登録成功
        // セッション開始
        SessionManager::startSession($userId);
        $sessionId = session_id();

        // その他のデータ取得とレスポンスの準備
        $this->prepareAndSendResponse($userId, $sessionId);
      } else {
        // 登録失敗（例えば、既に存在するメールアドレスなど）
        Response::sendError(400, 'User registration failed');
      }
    } catch (\PDOException $e) {
      // データベースエラーのハンドリング
      Response::sendError(500, $e->getMessage());
    }
  }

  private function prepareAndSendResponse($userId, $sessionId)
  {
    // コードマスタデータの取得
    $codeMasterData = $this->codeMasterModel->getAllCodeMasterData();

    // カテゴリデータの取得
    $majorCategories = $this->majorCategoryModel->getAllMajorCategories();
    $minorCategories = $this->minorCategoryModel->getAllMinorCategories();

    // 色データの取得
    $colors = $this->colorModel->getAllColors();

    // レスポンスとしてユーザーID、セッションID、その他データを返す
    Response::sendJSON([
      'userId' => $userId,
      'sessionId' => $sessionId,
      'codeMaster' => $codeMasterData,
      'major_category' => $majorCategories,
      'minor_category' => $minorCategories,
      'colors' => $colors
    ]);
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
      // ユーザーを検索
      $user =  $this->userModel->findByEmailAndPassword($email, $password);
      return $user;
    } catch (\PDOException $e) {
      // データベースエラーのハンドリング
      Response::sendError(500);
    }
  }

  private function validateEmail($email)
  {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
  }

  private function validatePassword($password)
  {
    return preg_match('/^[a-zA-Z0-9]+$/', $password);
  }
}
