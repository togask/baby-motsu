<?php
$requestUri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// ルーティングの定義
$routes = [
  'GET' => [
    '/api/example' => ['controller' => 'ExampleController', 'method' => 'search']
  ],
  'POST' => [
    '/api/example' => ['controller' => 'ExampleController', 'method' => 'create']
  ],
  'PUT' => [
    '/api/example' => ['controller' => 'ExampleController', 'method' => 'update']
  ],
  'PATCH' => [
    '/api/example' => ['controller' => 'ExampleController', 'method' => 'update']
  ],
  'DELETE' => [
    '/api/example' => ['controller' => 'ExampleController', 'method' => 'delete']
  ],
];

// リクエストに一致するルーティングを探す
if (isset($routes[$method]) && array_key_exists($requestUri, $routes[$method])) {
  $controllerName = $routes[$method][$requestUri]['controller'];
  $methodName = $routes[$method][$requestUri]['method'];

  // コントローラーのインスタンス化とメソッドの呼び出し
  $controller = new $controllerName();
  $controller->$methodName();
} else {
  // 404 Not Found
  header("HTTP/1.0 404 Not Found");
}
