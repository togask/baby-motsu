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

  public function getProductsByUserId($userId, $limit = null)
  {
    $query = "
        SELECT p.*, pi.path AS productImagePath, 
               (CASE WHEN p.status_id = 261 THEN true ELSE false END) AS isSold
        FROM PRODUCT p
        LEFT JOIN PRODUCT_IMAGE pi ON p.product_id = pi.product_id AND pi.order = 1
        WHERE p.seller_id = :userId
        ORDER BY p.datetime DESC";

    // LIMIT句の適用
    $this->applyLimitClause($query, $limit);

    $stmt = $this->db->prepare($query);
    $this->db->execute($stmt, ['userId' => $userId]);
    return $this->db->fetchAll($stmt);
  }

  public function searchProducts($searchCriteria, $limit = null)
  {
    $query = "
        SELECT p.*, pi.path AS productImagePath, 
               (CASE WHEN p.status_id = 261 THEN true ELSE false END) AS isSold
        FROM PRODUCT p
        LEFT JOIN PRODUCT_IMAGE pi ON p.product_id = pi.product_id AND pi.order = 1
        WHERE 1=1";

    $parameters = [];

    // 検索条件を適用
    foreach ($searchCriteria as $key => $value) {
      if (!empty($value)) {
        switch ($key) {
          case 'keyword':
            $query .= " AND p.product_name LIKE :keyword";
            $parameters['keyword'] = "%" . $value . "%";
            break;
          case 'excludeKeyword':
            $query .= " AND p.product_name NOT LIKE :excludeKeyword";
            $parameters['excludeKeyword'] = "%" . $value . "%";
            break;
          case 'age':
          case 'weight':
          case 'height':
          case 'brand':
          case 'productCondition':
          case 'color':
          case 'shippingFee':
          case 'shippingMethod':
            // $valueが配列であることを確認する
            if (!is_array($value)) {
              $value = [$value];  // 配列に変換
            }

            $placeholders = array_map(function ($i) use ($key) {
              return ":{$key}_{$i}";
            }, array_keys($value));
            $query .= " AND p.{$key}_id IN (" . implode(',', $placeholders) . ")";
            foreach ($value as $i => $val) {
              $parameters["{$key}_{$i}"] = $val;
            }
            break;
          case 'minPrice':
            $query .= " AND p.price >= :minPrice";
            $parameters['minPrice'] = $value;
            break;
          case 'maxPrice':
            $query .= " AND p.price <= :maxPrice";
            $parameters['maxPrice'] = $value;
            break;
        }
      }
    }

    // LIMIT句の適用
    $this->applyLimitClause($query, $limit);

    $stmt = $this->db->prepare($query);
    $this->db->execute($stmt, $parameters);
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

    // LIMIT句の適用
    $this->applyLimitClause($query, $limit);

    $stmt = $this->db->prepare($query);
    $this->db->execute($stmt);
    return $this->db->fetchAll($stmt);
  }

  private function applyLimitClause(&$query, $limit)
  {
    if ($limit !== null && is_numeric($limit)) {
      $query .= " LIMIT " . intval($limit);
    }
  }
}
