<?php
class SearchController
{
  private $db;
  private $productModel;

  public function __construct(Database $db)
  {
    $this->db = $db;
    $this->productModel = new ProductModel($this->db);
  }

  public function searchProducts()
  {
    $searchCriteria = [
      'keyword' => $_GET['keyword'] ?? '',
      'excludeKeyword' => $_GET['excludeKeyword'] ?? null,
      'age' => $_GET['age'] ?? [],
      'weight' => $_GET['weight'] ?? [],
      'height' => $_GET['height'] ?? [],
      'majorCategory' => $_GET['majorCategory'] ?? [],
      'brand' => $_GET['brand'] ?? [],
      'productCondition' => $_GET['productCondition'] ?? [],
      'color' => $_GET['color'] ?? [],
      'shippingFee' => $_GET['shippingFee'] ?? [],
      'shippingMethod' => $_GET['shippingMethod'] ?? [],
      'maxPrice' => $_GET['maxPrice'] ?? null,
      'minPrice' => $_GET['minPrice'] ?? null
    ];

    try {
      $products = $this->productModel->searchProducts($searchCriteria);
      $formattedProducts = array_map([ProductFormatter::class, 'format'], $products);
      Response::sendJSON($formattedProducts);
    } catch (\PDOException $e) {
      Response::sendError(500, $e->getMessage());
    }
  }
}
