<?php
class TransactionModel
{
  private $db;

  public function __construct(Database $database)
  {
    $this->db = $database;
  }

  public function getCompleteTransactionDetails($transactionId)
  {
    $stmt = $this->db->prepare("
            SELECT 
                t.transaction_status_id, t.amount, t.datetime,
                s.user_id AS sellerId, s.nickname AS sellerNickname, s.profile_image_path AS sellerProfileImagePath,
                p.product_id, p.product_name AS productName, 
                pi.path AS productImagePath,
                p.shipping_fee_responsibility_id,
                cm.name AS shippingFeeResponsibility,
                b.user_id AS buyerId, b.name AS buyerName, b.address AS buyerAddress,
                e.score, e.comment, 
                eu.nickname AS evaluatorNickname
            FROM TRANSACTION t
            LEFT JOIN USER s ON t.seller_id = s.user_id
            LEFT JOIN USER b ON t.buyer_id = b.user_id
            LEFT JOIN PRODUCT p ON t.product_id = p.product_id
            LEFT JOIN PRODUCT_IMAGE pi ON p.product_id = pi.product_id AND pi.`order` = 1
            LEFT JOIN CODE_MASTER cm ON p.shipping_fee_responsibility_id = cm.code_id
            LEFT JOIN EVALUATION e ON t.transaction_id = e.transaction_id
            LEFT JOIN USER eu ON e.evaluator_id = eu.user_id
            WHERE t.transaction_id = :transactionId
        ");
    $this->db->execute($stmt, ['transactionId' => $transactionId]);
    return $this->db->fetch($stmt);
  }
}
