<?php
class UsersController
{
  private $db;

  public function __construct(Database $db)
  {
    $this->db = $db;
  }

  public function mypage($userId)
  {
    try {
      $userModel = new UserModel($this->db);
      $productModel = new ProductModel($this->db);

      // ユーザープロフィールの取得
      $userProfile = $userModel->getUserProfile($userId);

      // ユーザーが出品している商品の取得
      $userProducts = $productModel->getProductsByUserId($userId);

      $formattedProducts = array_map([ProductFormatter::class, 'format'], $userProducts);

      // レスポンスデータの作成
      $response = [
        'userId' => $userId,
        'isSelf' => SessionManager::isCurrentUser($userId),
        'profile' => $userProfile,
        'products' => $formattedProducts
      ];

      // JSONとして出力
      Response::sendJSON($response);
    } catch (\Exception $e) {
      // エラーの場合はエラーレスポンスを返す
      Response::sendError(500, $e->getMessage());
    }
  }
}
