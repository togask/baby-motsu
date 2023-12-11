<?php
class TransactionController
{
  private $transactionModel;

  public function __construct(TransactionModel $transactionModel)
  {
    $this->transactionModel = $transactionModel;
  }

  public function getTransactionDetails($transactionId)
  {
    // HTTPヘッダーからuserIdとsessionIdを取得
    $headers = getallheaders();
    $userId = $headers['UserId'] ?? null;
    $sessionId = $headers['SessionId'] ?? null;

    // セッションの検証
    SessionManager::startSession();
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
        "date" => $transactionDetails['date'],
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
