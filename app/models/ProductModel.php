<?php
class ProductModel
{
  private $db;

  public function __construct(Database $database)
  {
    $this->db = $database;
  }

  public function getProductDetails($productId, $currentUserId)
  {
    $query = "
        SELECT 
          p.product_id, p.seller_id, p.product_name, p.product_description, p.price, p.datetime,
          age.name as age, weight.name as weight, height.name as height,
          major_category.major_category_id, major_category.major_category as major_category_name,
          minor_category.minor_category_id, minor_category.minor_category as minor_category_name,
          brand.name as brand, color.color_id, color.color as color_name, color.color_code,
          product_condition.name as `condition`,
          shipping_fee_responsibility.name as shipping_fee_responsibility,
          shipping_method.name as shipping_method,
          shipping_origin_region.name as shipping_origin_region,
          day_to_ship.name as day_to_ship, 
          status.name as status,
          (CASE WHEN p.status_id = 261 THEN true ELSE false END) AS isSold,
          CASE 
            WHEN :currentUserId1 IS NOT NULL 
            THEN EXISTS(SELECT 1 FROM FAVORITE WHERE product_id = p.product_id AND user_id = :currentUserId2) 
            ELSE false 
          END AS isLiked
        FROM 
          PRODUCT p
        LEFT JOIN CODE_MASTER age ON p.age_id = age.code_id
        LEFT JOIN CODE_MASTER weight ON p.weight_id = weight.code_id
        LEFT JOIN CODE_MASTER height ON p.height_id = height.code_id
        LEFT JOIN MAJOR_CATEGORY major_category ON p.major_category_id = major_category.major_category_id
        LEFT JOIN MINOR_CATEGORY minor_category ON p.minor_category_id = minor_category.minor_category_id
        LEFT JOIN CODE_MASTER brand ON p.brand_id = brand.code_id
        LEFT JOIN COLOR color ON p.color_id = color.color_id
        LEFT JOIN CODE_MASTER product_condition ON p.product_condition_id = product_condition.code_id
        LEFT JOIN CODE_MASTER shipping_fee_responsibility ON p.shipping_fee_responsibility_id = shipping_fee_responsibility.code_id
        LEFT JOIN CODE_MASTER shipping_method ON p.shipping_method_id = shipping_method.code_id
        LEFT JOIN CODE_MASTER shipping_origin_region ON p.shipping_origin_region_id = shipping_origin_region.code_id
        LEFT JOIN CODE_MASTER day_to_ship ON p.day_to_ship_id = day_to_ship.code_id
        LEFT JOIN CODE_MASTER status ON p.status_id = status.code_id
        WHERE p.product_id = :productId;";

    $stmt = $this->db->prepare($query);
    $this->db->execute($stmt, [
      'productId' => $productId,
      'currentUserId1' => $currentUserId ?? null,
      'currentUserId2' => $currentUserId ?? null
    ]);
    return $this->db->fetch($stmt);
  }

  public function searchProductsByKeyword($keyword, $limit = null)
  {
    $query = "
        SELECT p.*, pi.path AS productImagePath, 
               (CASE WHEN p.status_id = 261 THEN true ELSE false END) AS isSold
        FROM PRODUCT p
        LEFT JOIN PRODUCT_IMAGE pi ON p.product_id = pi.product_id AND pi.order = 1
        WHERE p.product_name LIKE :keyword
        ORDER BY p.datetime DESC";

    // limitが指定されている場合は、クエリにLIMIT句を追加
    if ($limit !== null && is_numeric($limit)) {
      $query .= " LIMIT " . intval($limit);
    }

    $stmt = $this->db->prepare($query);
    $this->db->execute($stmt, ['keyword' => "%{$keyword}%"]);
    return $this->db->fetchAll($stmt);
  }

  public function getProductsByUserId($userId, $limit = null)
  {
    $query = "
        SELECT p.*, pi.path AS productImagePath, 
               (CASE WHEN p.status_id = 261 THEN true ELSE false END) AS isSold
        FROM PRODUCT p
        LEFT JOIN PRODUCT_IMAGE pi ON p.product_id = pi.product_id AND pi.order = 1
        WHERE p.seller_id = :userId
        ORDER BY p.datetime DESC";

    // limitが指定されている場合は、クエリにLIMIT句を追加
    if ($limit !== null && is_numeric($limit)) {
      $query .= " LIMIT " . intval($limit);
    }

    $stmt = $this->db->prepare($query);
    $this->db->execute($stmt, ['userId' => $userId]);
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
