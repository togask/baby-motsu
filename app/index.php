<?php
// モデルの読み込み
require_once 'models/UserModel.php';
require_once 'models/CodeMasterModel.php';
require_once 'models/MajorCategoryModel.php';
require_once 'models/MinorCategoryModel.php';
require_once 'models/ColorModel.php';
require_once 'models/ProductModel.php';
// コントローラーの読み込み
require_once 'controllers/AuthController.php';
require_once 'controllers/SearchController.php';
// コアの読み込み
require_once 'core/Database.php';
require_once 'core/Response.php';
require_once 'core/SessionManager.php';

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
    '/api/search/' => ['controller' => 'SearchController', 'method' => 'searchProducts'],
  ],
  'POST' => [
    '/api/auth/' => ['controller' => 'AuthController', 'method' => 'login'],
  ],
];

// ルーティングのマッチングとコントローラーの呼び出し
if (isset($routes[$method]) && array_key_exists($path, $routes[$method])) {
  $controllerName = $routes[$method][$path]['controller'];
  $methodName = $routes[$method][$path]['method'];

  $controller = new $controllerName();
  $controller->$methodName();
} else {
  // 404 Not FoundレスポンスをResponseクラスを使用して送信
  Response::sendError(404);
}
