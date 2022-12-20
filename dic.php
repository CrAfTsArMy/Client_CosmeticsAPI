<?php

require_once __DIR__ . "/controller/CosmeticsController.php";

$container = [];

$container[MySQL::class] = function () {
    $config = include __DIR__ . '/utils/config.php';
    $mysql = new MySQL("127.0.0.1:3306", $config['mysql']['user'], $config['mysql']['password'], "cosmetics");
    $mysql->connect();
    return $mysql;
};

$container[CosmeticsController::class] = function () use ($container) {
    return new CosmeticsController($container[MySQL::class]());
};

$container[Router::class] = function () use ($container) {
    $router = new Router();

    foreach ($container as $className => $service) {
        $reflectionClass = new ReflectionClass($className);
        $methods = $reflectionClass->getMethods();
        foreach ($methods as $method) {
            $attributes = $method->getAttributes(Route::class);
            foreach ($attributes as $attribute) {
                /** @var Route $route */
                $route = $attribute->newInstance();
                $route->setAction([$service(), $method->getName()]);

                $router->register($route);
            }
        }
    }

    return $router;
};

return $container;