<?php

namespace App\Facades\Routing;

use App\Controllers\Controller;
use App\Exceptions\CantResolveDependenciesException;
use App\Exceptions\MethodNotAllowedException;
use App\Exceptions\UndefinedMethodException;
use App\Facades\Http\Request;
use App\Facades\Http\Response;

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

    /** @var array */
    protected $routeParams = [];

    /** @var Request */
    protected $request;

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

    /**
     * @param array $params
     *
     * @return $this
     */
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

    /**
     * @param Request $request
     *
     * @return $this
     */
    public function setRequest(Request $request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @throws UndefinedMethodException
     * @throws \ReflectionException|CantResolveDependenciesException
     */
    public function handle(): Response
    {
        if (count($this->action) !== 2 || ! class_exists($this->action[0])) {
            throw new \InvalidArgumentException('Invalid route action');
        }

        if (! method_exists($this->action[0], $this->action[1])) {
            throw new UndefinedMethodException('Undefined method: ' . $this->action[1] . '()');
        }

        try {
            $reflectionClass = new \ReflectionClass($this->action[0]);
            $reflectionConstructor = $reflectionClass->getConstructor();
            $reflectionMethod = $reflectionClass->getMethod($this->action[1]);

            $classConstructorParams = [];
            if ($reflectionConstructor) {
                foreach ($reflectionConstructor->getParameters() as $item) {
                    $classParam = $item->getClass()->getName();
                    $classConstructorParams[] = new $classParam();
                }
            }
            $class = new $this->action[0](...$classConstructorParams);

            $methodParams = [];
            foreach ($reflectionMethod->getParameters() as $item) {
                $typeParam = $item->getClass();
                if ($typeParam) {
                    $typeParam = $typeParam->getName();
                }
                $nameParam = $item->getName();
                if ($typeParam === Request::class) {
                    $methodParams[] = $this->request;
                    continue;
                }
                if (class_exists($typeParam) || array_key_exists($nameParam, $this->routeParams)) {
                    $methodParams[] = class_exists($typeParam)
                        ? new $typeParam()
                        : $this->routeParams[$nameParam] ?? null;
                } else if ($item->isDefaultValueAvailable()) {
                    $methodParams[] = $item->getDefaultValue();
                } else {
                    throw new \InvalidArgumentException('Invalid method params');
                }
            }
        } catch (\Exception $exception) {
            throw new CantResolveDependenciesException($exception->getMessage(), $exception->getCode(), $exception);
        }

        if ($class instanceof Controller) {
            $class->setRequest($this->request)
                ->authorize()
                ->validate();
        }

        $response = $reflectionMethod->invokeArgs($class, $methodParams);

        if ($response instanceof Response) {
            return $response;
        }

        return new Response([], Response::HTTP_NO_CONTENT, [Response::CONTENT_TYPE_JSON]);
    }
}