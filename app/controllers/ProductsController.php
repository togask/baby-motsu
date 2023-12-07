<?php
class ProductsController
{
  public function index()
  {
    try {
      // 商品モデルのインスタンス化
      $productModel = new ProductModel();
      $products = $productModel->getAllProducts();

      // フォーマットされた商品情報の取得
      $formattedProducts = $this->formatProducts($products);

      // JSONとして出力
      Response::sendJSON($formattedProducts);
    } catch (\Exception $e) {
      // エラーの場合はエラーレスポンスを返す
      Response::sendError(500, $e->getMessage());
    }
  }

  private function formatProducts($products)
  {
    $formattedProducts = [];
    foreach ($products as $product) {
      $formattedProducts[] = [
        'productId' => $product['product_id'],
        'product' => [
          'productName' => $product['product_name'],
          'productImagePath' => $product['productImagePath'],
          'price' => $product['price'],
          'isSold' => $product['isSold'],
          'date' => $product['datetime']
        ]
      ];
    }
    return $formattedProducts;
  }
}
