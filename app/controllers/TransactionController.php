<?php
class TransactionController
{
  private $db;
  private $transactionModel;

  public function __construct(Database $db)
  {
    $this->db = $db;
    $this->transactionModel = new TransactionModel($this->db);
  }

  public function getTransactionDetails($transactionId)
  {
    // HTTPヘッダーからuserIdとsessionIdを取得
    $headers = array_change_key_case(getallheaders(), CASE_LOWER);
    $userId = $headers['userid'] ?? null;
    $sessionId = $headers['sessionid'] ?? null;

    // ユーザーIDとセッションIDの有効性を確認
    if ($userId === null || $sessionId === null) {
      Response::sendError(401, "Unauthorized access.");
      return;
    }

    // セッションの検証
    if (!SessionManager::checkSessionId($userId, $sessionId)) {
      Response::sendError(401, "Unauthorized access.");
      return;
    }

    // 取引情報の取得
    $transactionDetails = $this->transactionModel->getCompleteTransactionDetails($transactionId);
    if (!$transactionDetails) {
      Response::sendError(404, "Transaction not found.");
      return;
    }

    // isSellerの判定
    $isSeller = $transactionDetails['sellerId'] == $userId;
    $isBuyer = $transactionDetails['buyerId'] == $userId;

    if (!$isSeller && !$isBuyer) {
      // トランザクションに関与していないユーザーのアクセスを拒否
      Response::sendError(403, "Access denied.");
      return;
    }

    // 必要に応じて評価情報を取得
    $evaluationInfo = null;
    if (($transactionDetails['transaction_status_id'] == 265 && $isSeller) ||
      ($transactionDetails['transaction_status_id'] == 266)
    ) {
      // 評価情報の設定
      $evaluationInfo = [
        'nickname' => $transactionDetails['evaluatorNickname'],
        'comment' => $transactionDetails['comment'],
        'score' => $transactionDetails['score']
      ];
    }

    // レスポンスデータの構築
    $responseData = [
      "transactionStatusId" => $transactionDetails['transaction_status_id'],
      "isSeller" => $isSeller,
      "sellerInfo" => [
        "userId" => $transactionDetails['sellerId'],
        "nickname" => $transactionDetails['sellerNickname'],
        "profileImagePath" => $transactionDetails['sellerProfileImagePath']
      ],
      "productInfo" => [
        "productId" => $transactionDetails['product_id'],
        "productName" => $transactionDetails['productName'],
        "productImagePath" => $transactionDetails['productImagePath']
      ],
      "transactionInfo" => [
        "amount" => $transactionDetails['amount'],
        "shippingFeeResponsibility" => $transactionDetails['shippingFeeResponsibility'],
        "date" => $transactionDetails['datetime'],
        "productId" => $transactionDetails['product_id']
      ],
      "buyerInfo" => $isSeller ? null : [
        "name" => $transactionDetails['buyerName'],
        "address" => $transactionDetails['buyerAddress']
      ],
      "evaluationInfo" => $evaluationInfo
    ];

    Response::sendJSON($responseData);
  }
}
