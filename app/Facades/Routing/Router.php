<?php

namespace App\Facades\Routing;

use App\Exceptions\MethodNotAllowedException;
use App\Facades\Http\Request;
use App\Support\Arr;

class Router
{
    /** @var array */
    protected $routes = [];

    /** @var Request */
    protected $request;

    /**
     * Router constructor.
     */
    public function __construct()
    {
        $this->loadRoutes();
    }

    /**
     * Load routes.
     */
    protected function loadRoutes(): void
    {
        $routes = require_once __DIR__ . '/../../../routes/web.php';

        $routes = Arr::flatten($routes);

        /** @var Route $route */
        foreach ($routes as $route) {
            $this->routes[$route->getMethod()][] = $route;
        }
    }

    /**
     * @param Request $request
     *
     * @return self
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return string
     * @throws MethodNotAllowedException
     */
    public function handle(): string
    {
        return $this->getRouteByRequest()->setRequest($this->request)->exec();
    }

    /**
     * @return Route
     * @throws MethodNotAllowedException
     */
    protected function getRouteByRequest(): Route
    {
        $routesForCurrentMethod = $this->routes[$this->request->getMethod()] ?? [];

        $requestUri = trim($this->request->getUri(), '/');

        /** @var Route $route */
        foreach ($routesForCurrentMethod as $route) {
            $routeUri = trim($route->getUri(), '/');
            if (
                preg_match('/^[[:alnum:]_-]+(\/[[:alnum:]_-]+)*$/', $routeUri)
                && $routeUri === $requestUri
            ) {
                return $route;
            } elseif (preg_match_all('/{([[:alnum:]_-]+)}/', $routeUri, $matches)) {
                $routeParams = $matches[1];
                $routePattern = preg_replace('/{([[:alnum:]_-]+)}/', '(?<$1>[[:alnum:]_-]+)', $routeUri);
                if (
                    preg_match('/^' . $routePattern . '$/', $requestUri, $matches)
                    && count($routeParams) === count($matches) - 1
                ) {
                    $route->setRouteParams(array_combine($routeParams, array_slice($matches, 1)));
                    return $route;
                }
            }
        }

        throw new MethodNotAllowedException();
    }
}