<?php
class ProductImageModel
{
  private $db;

  public function __construct(Database $database)
  {
    $this->db = $database;
  }

  public function getProductImages($productId)
  {
    $stmt = $this->db->prepare("
            SELECT path, `order`
            FROM PRODUCT_IMAGE
            WHERE product_id = :productId
            ORDER BY `order`
        ");
    $this->db->execute($stmt, ['productId' => $productId]);
    return $this->db->fetchAll($stmt);
  }
}
