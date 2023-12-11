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
    $parameters = [':email' => $email];
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
}
