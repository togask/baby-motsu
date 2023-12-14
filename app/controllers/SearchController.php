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
    $keyword = $_GET['keyword'] ?? '';

    if (!$keyword) {
      Response::sendError(400, 'No keyword provided');
      return;
    }

    try {
      $products = $this->productModel->searchProductsByKeyword($keyword);

      $formattedProducts = array_map([ProductFormatter::class, 'format'], $products);

      Response::sendJSON($formattedProducts);
    } catch (\PDOException $e) {
      Response::sendError(500, $e->getMessage());
    }
  }
}
