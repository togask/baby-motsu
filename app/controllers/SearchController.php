<?php
class SearchController
{
  public function searchProducts()
  {
    $keyword = $_GET['keyword'] ?? '';

    if (!$keyword) {
      Response::sendError(400, 'No keyword provided');
      return;
    }

    try {
      $productModel = new ProductModel();
      $products = $productModel->searchProductsByKeyword($keyword);

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

      Response::sendJSON($formattedProducts);
    } catch (\PDOException $e) {
      Response::sendError(500, $e->getMessage());
    }
  }
}