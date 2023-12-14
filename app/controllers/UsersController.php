<?php
class UsersController
{
  private $db;
  private $userModel;
  private $productModel;

  public function __construct(Database $db)
  {
    $this->db = $db;
    $this->userModel = new UserModel($this->db);
    $this->productModel = new ProductModel($this->db);
  }

  public function mypage($userId)
  {
    try {
      // ユーザープロフィールの取得
      $userProfile = $this->userModel->getUserProfile($userId);

      // ユーザーが出品している商品の取得
      $userProducts = $this->productModel->getProductsByUserId($userId);

      // レスポンスデータの作成
      $formattedProducts = array_map([ProductFormatter::class, 'format'], $userProducts);
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
