<?php
error_reporting(E_ALL);

require_once __DIR__ . '/router/Router.php';
require_once __DIR__ . '/router/Request.php';
require_once __DIR__ . '/router/RouteNotFoundException.php';
require_once __DIR__ . '/router/Route.php';

require_once __DIR__ . '/utils/config.php';
require_once __DIR__ . '/utils/json.php';
require_once __DIR__ . '/utils/mysql.php';

$container = require_once __DIR__ . '/dic.php';

$router = $container[Router::class]();

$request = Request::createFromGlobal();

try {
    echo $router->handle($request);
} catch (RouteNotFoundException $exception) {
    echo $exception->getMessage();
}