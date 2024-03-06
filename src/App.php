<?php

use App\Providers\Router;
use App\Controller\ProductController;


$router = new Router();

$router->addRoute('GET', '/', function () {
    $controller = new ProductController();
    $controller->index();
    exit;
});

$router->addRoute('GET', '/products/show', function () {
    $controller = new ProductController();
    $controller->show();
    exit;
});

$router->addRoute('GET', '/products/show/:id', function () {
    $controller = new ProductController();
    $controller->show($_GET["id"]);
    exit;
});

$router->addRoute('POST', '/products/store', function () {
    $controller = new ProductController();
    $controller->store();
    exit;
});

$router->addRoute('POST', '/products/delete', function () {
    $controller = new ProductController();
    $controller->delete($_POST["product"]);
    exit;
});

$router->addRoute('POST', '/products/:id', function () {
    $controller = new ProductController();
    $controller->update($_GET["id"]);
    exit;
});




$router->matchRoute();