<?php
class ProductModel
{
  private $db;

  public function __construct()
  {
    $this->db = new Database();
  }

  public function searchProductsByKeyword($keyword)
  {
    $stmt = $this->db->prepare("
            SELECT p.*, pi.path AS productImagePath,
                    (CASE WHEN p.status_id = 261 THEN true ELSE false END) AS isSold
          FROM PRODUCT p
          LEFT JOIN PRODUCT_IMAGE pi ON p.product_id = pi.product_id AND pi.order = 1
          WHERE p.product_name LIKE :keyword
          ORDER BY p.datetime DESC
        ");
    $this->db->execute($stmt, ['keyword' => "%{$keyword}%"]);
    return $this->db->fetchAll($stmt);
  }

  public function getAllProducts()
  {
    $stmt = $this->db->prepare("
            SELECT p.*, pi.path AS productImagePath, 
                   (CASE WHEN p.status_id = 261 THEN true ELSE false END) AS isSold
            FROM PRODUCT p
            LEFT JOIN PRODUCT_IMAGE pi ON p.product_id = pi.product_id AND pi.order = 1
            ORDER BY p.datetime DESC
        ");
    $this->db->execute($stmt);
    return $this->db->fetchAll($stmt);
  }
}
