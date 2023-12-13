<?php
class ProductModel
{
  private $db;

  public function __construct(Database $database)
  {
    $this->db = $database;
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

  public function getAllProducts($limit = null)
  {
    $query = "
        SELECT p.*, pi.path AS productImagePath, 
               (CASE WHEN p.status_id = 261 THEN true ELSE false END) AS isSold
        FROM PRODUCT p
        LEFT JOIN PRODUCT_IMAGE pi ON p.product_id = pi.product_id AND pi.order = 1
        ORDER BY p.datetime DESC";

    // limitが指定されている場合は、クエリにLIMIT句を追加
    if ($limit !== null && is_numeric($limit)) {
      $query .= " LIMIT " . intval($limit);
    }

    $stmt = $this->db->prepare($query);
    $this->db->execute($stmt);
    return $this->db->fetchAll($stmt);
  }
}
