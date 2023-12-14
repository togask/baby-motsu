<?php
class ReviewModel
{
  private $db;

  public function __construct(Database $database)
  {
    $this->db = $database;
  }

  public function getProductReviews($productId)
  {
    $stmt = $this->db->prepare("
            SELECT r.comment, cm.name as usageDuration
            FROM REVIEW r
            JOIN CODE_MASTER cm ON r.usage_duration_id = cm.code_id
            WHERE r.product_id = :productId
        ");
    $this->db->execute($stmt, ['productId' => $productId]);
    return $this->db->fetchAll($stmt);
  }
}
