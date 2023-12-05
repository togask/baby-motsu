<?php
class UserModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
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
    $this->db->execute($stmt, ['email' => $email]);

    // ユーザーデータを取得
    $user = $this->db->fetch($stmt);

    if ($user && password_verify($password, $user['password'])) {
      // パスワードが一致する場合、ユーザーオブジェクトを返す
      return $user;
    }

    // ユーザーが見つからないか、パスワードが一致しない場合はfalseを返す
    return false;
  }
}
