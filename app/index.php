<?php
// CORSヘッダ
header('Access-Control-Allow-Origin: https://aso2201373.angry.jp');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
// モデルの読み込み
require_once 'models/UserModel.php';
require_once 'models/CodeMasterModel.php';
require_once 'models/MajorCategoryModel.php';
require_once 'models/MinorCategoryModel.php';
require_once 'models/ColorModel.php';
require_once 'models/ProductModel.php';
require_once 'models/TransactionModel.php';
// コントローラーの読み込み
require_once 'controllers/AuthController.php';
require_once 'controllers/SearchController.php';
require_once 'controllers/ProductsController.php';
require_once 'controllers/TransactionController.php';
// コアの読み込み
require_once 'core/Database.php';
require_once 'core/Response.php';
require_once 'core/SessionManager.php';

// データベース接続の初期化
$db = new Database();

// SessionManagerの初期化
SessionManager::initialize($db);

$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// クエリパラメータを除去
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'];

// ベースパスを取り除く
$basePath = '/backend';
$path = str_replace($basePath, '', $path);

// ルーティングの定義
$routes = [
  'GET' => [
    '/api/search' => ['controller' => 'SearchController', 'method' => 'searchProducts'],
    '/api/products' => ['controller' => 'ProductsController', 'method' => 'index'],
    '/api/transactions/{transactionId}' => ['controller' => 'TransactionController', 'method' => 'getTransactionDetails'],
  ],
  'POST' => [
    '/api/auth' => ['controller' => 'AuthController', 'method' => 'login'],
  ],
];

// ルーティングのマッチングとコントローラーの呼び出し
foreach ($routes[$method] as $route => $action) {
  $pattern = preg_replace('/{[^}]+}/', '([^/]+)', $route);
  if (preg_match('#^' . $pattern . '$#', $path, $matches)) {
    array_shift($matches); // フルマッチ部分を削除

    $controllerName = $action['controller'];
    $methodName = $action['method'];

    $controller = new $controllerName();
    call_user_func_array([$controller, $methodName], $matches);
    break;
  }
}

if (!isset($controller)) {
  Response::sendError(404); // ルートが見つからない場合
}
