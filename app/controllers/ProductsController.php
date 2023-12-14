<?php
class ProductsController
{
  private $db;
  private $productModel;
  private $userModel;
  private $productImageModel;
  private $reviewModel;
  private $transactionModel;

  public function __construct(Database $db)
  {
    $this->db = $db;
    $this->productModel = new ProductModel($this->db);
    $this->userModel = new UserModel($this->db);
    $this->productImageModel = new ProductImageModel($this->db);
    $this->reviewModel = new ReviewModel($this->db);
    $this->transactionModel = new TransactionModel($this->db);
  }

  public function index()
  {
    try {
      // 最新の60件の商品リストを取得
      $products = $this->productModel->getAllProducts(60);

      // フォーマットされた商品情報の取得
      $formattedProducts = array_map([ProductFormatter::class, 'format'], $products);

      // JSONとして出力
      Response::sendJSON($formattedProducts);
    } catch (\Exception $e) {
      // エラーの場合はエラーレスポンスを返す
      Response::sendError(500, $e->getMessage());
    }
  }

  public function getProduct($productId)
  {
    try {
      // 商品の基本情報を取得
      $productDetails = $this->productModel->getProductDetails($productId, SessionManager::get('userId'));
      if (!$productDetails) {
        Response::sendError(404, 'Product not found');
        return;
      }

      // 商品の画像を取得
      $images = $this->productImageModel->getProductImages($productId);

      // 出品者の情報を取得
      $seller = $this->userModel->getUserProfile($productDetails['seller_id']);

      // 1の場合、true (ブール値) を返す
      $isSold = $productDetails['isSold'] == 1;

      // isSoldがtrueの場合、isBuyerを判定
      $isBuyer = false;
      if ($isSold) {
        $transaction = $this->transactionModel->getTransactionByProductId($productId);
        $currentUserId = SessionManager::get('userId');
        $isBuyer = $transaction && $transaction['buyer_id'] == $currentUserId;
      }

      // 商品のレビューを取得
      $reviews = $this->reviewModel->getProductReviews($productId);

      $response = [
        'productId' => $productId,
        'images' => $images,
        'productName' => $productDetails['product_name'],
        'productDescription' => $productDetails['product_description'],
        'price' => $productDetails['price'],
        'itemAttributes' => [
          'age' => $productDetails['age'],
          'height' => $productDetails['height'],
          'weight' => $productDetails['weight'],
          'brand' => $productDetails['brand'],
          'majorCategory' => [
            'id' => $productDetails['major_category_id'],
            'name' => $productDetails['major_category_name']
          ],
          'minorCategory' => [
            'id' => $productDetails['minor_category_id'],
            'name' => $productDetails['minor_category_name']
          ],
          'color' => [
            'colorId' => $productDetails['color_id'],
            'name' => $productDetails['color_name'],
            'colorCode' => $productDetails['color_code']
          ],
          'condition' => $productDetails['condition']
        ],
        'shippingDetails' => [
          'shippingFeeResponsibility' => $productDetails['shipping_fee_responsibility'],
          'shippingMethod' => $productDetails['shipping_method'],
          'shippingOriginRegion' => $productDetails['shipping_origin_region'],
          'dayToShip' => $productDetails['day_to_ship']
        ],
        'isLiked' => $productDetails['isLiked'],
        'isSold' => $isSold,
        'isSeller' => SessionManager::isCurrentUser($productDetails['seller_id']),
        'isBuyer' => $isBuyer,
        'seller' => $seller,
        'reviews' => array_map(function ($review) {
          return [
            'usageDuration' => $review['usageDuration'],
            'comment' => $review['comment']
          ];
        }, $reviews),
      ];

      // JSONとして出力
      Response::sendJSON($response);
    } catch (\Exception $e) {
      // エラーの場合はエラーレスポンスを返す
      Response::sendError(500, $e->getMessage());
    }
  }
}
