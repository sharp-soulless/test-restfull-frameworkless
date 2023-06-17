<?php

require __DIR__ . '/vendor/autoload.php';

use App\Exceptions\MethodNotAllowedException;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\UndefinedMethodException;
use App\Facades\Http\{
    Request,
    Response
};
use App\Facades\Routing\Router;

$router = new Router();

//return (new Response([$_SERVER, $_REQUEST], 200, [Response::CONTENT_TYPE_JSON]))->toJson();

try {
    $response = $router->setRequest(Request::make())->handle();
} catch (UnauthorizedException $exception) {
    // log exception
    $response = new Response(
        ['message' => 'Unauthorized'],
        Response::HTTP_UNAUTHORIZED,
        [Response::CONTENT_TYPE_JSON]
    );
} catch (MethodNotAllowedException | UndefinedMethodException $exception) {
    // log exception
    $response = new Response(
        ['message' => 'Method not allowed'],
        Response::HTTP_METHOD_NOT_ALLOWED,
        [Response::CONTENT_TYPE_JSON]
    );
} catch (\Exception $exception) {
    // log exception
    $response = new Response(
        [
            'message' => $exception->getCode() > 499
                ? $exception->getMessage()//'Server error'
                : $exception->getMessage()
        ],
        $exception->getCode() > 499
            ? Response::HTTP_INTERNAL_SERVER_ERROR
            : $exception->getCode(),
        [Response::CONTENT_TYPE_JSON]
    );
}

return $response->toJson();
