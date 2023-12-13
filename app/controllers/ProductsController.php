<?php
class ProductsController
{
  private $db;

  public function __construct(Database $db)
  {
    $this->db = $db;
  }

  public function index()
  {
    try {
      // 商品モデルのインスタンス化
      $productModel = new ProductModel($this->db);
      $products = $productModel->getAllProducts(60);

      // フォーマットされた商品情報の取得
      $formattedProducts = array_map([ProductFormatter::class, 'format'], $products);

      // JSONとして出力
      Response::sendJSON($formattedProducts);
    } catch (\Exception $e) {
      // エラーの場合はエラーレスポンスを返す
      Response::sendError(500, $e->getMessage());
    }
  }
}
