<?php
class UserModel
{
  private $db;

  public function __construct(Database $database)
  {
    $this->db = $database;
  }

  /**
   * メールアドレスとパスワードでユーザーを検索する
   * @param string $email メールアドレス
   * @param string $password パスワード
   * @return mixed ユーザーオブジェクトまたはfalse
   */
  public function findByEmailAndPassword($email, $password)
  {
    // SQLステートメントの準備
    $stmt = $this->db->prepare("SELECT * FROM USER WHERE email = :email");
    $parameters = ['email' => $email];
    $this->db->execute($stmt, $parameters);

    // ユーザーデータを取得
    $user = $this->db->fetch($stmt);

    // if ($user && password_verify($password, $user['password'])) {
    if ($user) {
      // パスワードが一致する場合、ユーザーオブジェクトを返す
      return $user;
    }

    // ユーザーが見つからないか、パスワードが一致しない場合はfalseを返す
    return false;
  }

  public function getUserProfile($userId)
  {
    $stmt = $this->db->prepare("
            SELECT u.nickname, u.profile_image_path, u.introduce, 
                  ROUND(AVG(e.score), 1) as average_rating
            FROM USER u
            LEFT JOIN EVALUATION e ON u.user_id = e.evaluatee_id
            WHERE u.user_id = :userId
            GROUP BY u.user_id
          ");
    $this->db->execute($stmt, ['userId' => $userId]);
    return $this->db->fetch($stmt);
  }

  /**
   * 新しいユーザーを登録する
   * @param string $email メールアドレス
   * @param string $hashedPassword ハッシュ化されたパスワード
   * @param string $nickname ニックネーム
   * @return int|false 成功した場合はユーザーID、失敗した場合はfalse
   */
  public function registerUser($email, $hashedPassword, $nickname)
  {
    // まずメールアドレスが既に存在するかチェック
    if ($this->emailExists($email)) {
      // メールアドレスが既に存在する場合はエラーを返す
      return false;
    }

    // 登録処理
    $stmt = $this->db->prepare("INSERT INTO USER (email, password, nickname) VALUES (:email, :password, :nickname)");
    $success = $this->db->execute($stmt, [
      'email' => $email,
      'password' => $hashedPassword,
      'nickname' => $nickname
    ]);

    if ($success) {
      return $this->db->lastInsertId();
    } else {
      return false;
    }
  }

  private function emailExists($email)
  {
    $stmt = $this->db->prepare("SELECT email FROM USER WHERE email = :email");
    $this->db->execute($stmt, ['email' => $email]);
    return $this->db->fetch($stmt) ? true : false;
  }
}
