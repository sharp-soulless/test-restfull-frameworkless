<?php

require __DIR__ . '/vendor/autoload.php';

use App\Facades\Routing\Router;
use App\Facades\Http\Request;

$router = new Router();

try {
    return $router->setRequest(Request::make())->handle();
} catch (\App\Exceptions\MethodNotAllowedException $e) {
    return new \App\Facades\Http\Response(); // TODO: make error response
}