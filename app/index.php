<?php
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// クエリパラメータを除去
$parsedUrl = parse_url($requestUri);
$path = $parsedUrl['path'];

// ルーティングの定義
$routes = [
  'GET' => [
    '/api/example' => ['controller' => 'ExampleController', 'method' => 'search'],
  ],
  'POST' => [
    '/api/example' => ['controller' => 'ExampleController', 'method' => 'create'],
    '/api/auth' => ['controller' => 'AuthController', 'method' => 'login'],
  ],
  'PUT' => [
    '/api/example' => ['controller' => 'ExampleController', 'method' => 'update'],
  ],
  'PATCH' => [
    '/api/example' => ['controller' => 'ExampleController', 'method' => 'update'],
  ],
  'DELETE' => [
    '/api/example' => ['controller' => 'ExampleController', 'method' => 'delete'],
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
  Response::sendResponse(404);
}
