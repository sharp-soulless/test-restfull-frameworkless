<?php

namespace App\Facades\Routing;

use App\Exceptions\UndefinedMethodException;

/**
 * @method static Route get(string $uri, array $action)
 * @method static Route post(string $uri, array $action)
 * @method static Route put(string $uri, array $action)
 * @method static Route patch(string $uri, array $action)
 * @method static Route delete(string $uri, array $action)
 */
class Route
{
    /**
     * @var array
     */
    const ALLOWED_METHODS = [
        'GET',
        'POST',
        'PUT',
        'PATCH',
        'DELETE',
    ];

    /** @var string */
    protected $method;

    /** @var string */
    protected $uri;

    /** @var array */
    protected $action;

    protected $routeParams = [];

    /**
     * @param string $method
     *
     * @return self
     */
    public function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param array $action
     *
     * @return self
     */
    public function setAction(array $action): self
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @param string $uri
     *
     * @return self
     */
    public function setUri(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    public function setRouteParams(array $params): self
    {
        $this->routeParams = array_merge($this->routeParams, $params);
        return $this;
    }

    /**
     * @return array
     */
    public function getAction(): array
    {
        return $this->action;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return string
     */
    public function getUri(): string
    {
        return $this->uri;
    }

    /**
     * @param string $uri
     * @param array $action
     *
     * @return array
     */
    public static function any(string $uri, array $action): array
    {
        return self::match(self::ALLOWED_METHODS, $uri, $action);
    }

    /**
     * @param array $methods
     * @param string $uri
     * @param array $action
     *
     * @return array
     */
    public static function match(array $methods, string $uri, array $action): array
    {
        $routes = [];
        foreach ($methods as $method){
            try {
                $routes[] = self::{$method}($uri, $action);
            } catch(UndefinedMethodException | \InvalidArgumentException $exception) {
                // log exception
            }
        }

        return $routes;
    }

    /**
     * @param string $name
     * @param array $arguments
     *
     * @return self
     * @throws UndefinedMethodException
     * @throws \InvalidArgumentException
     */
    public static function __callStatic(string $name, array $arguments): self
    {
        if (! in_array(strtoupper($name), self::ALLOWED_METHODS)) {
            throw new UndefinedMethodException('Undefined method: ' . $name . '()');
        }

        if (! is_string($arguments[0]) || ! is_array($arguments[1])) {
            throw new \InvalidArgumentException('Invalid arguments');
        }

        return (new self)->setMethod($name)
            ->setUri($arguments[0])
            ->setAction($arguments[1]);
    }
}