<?php

namespace App\Facades\Http;

class Request
{
    /** @var array  */
    protected $params = [];

    /** @var string */
    protected $method;

    /** @var string */
    protected $uri;

    /** @var array */
    protected $auth = [];

    /**
     * @return static
     */
    public static function make(): self
    {
        return (new self())
            ->setMethod($_SERVER['REQUEST_METHOD'])
            ->setUri(strpos($_SERVER['REQUEST_URI'], '?') !== false
                ? strstr($_SERVER['REQUEST_URI'], '?', true)
                : $_SERVER['REQUEST_URI']
            )
            ->setBasicAuth(
                [
                    'user' => $_SERVER['PHP_AUTH_USER'] ?? null,
                    'password' => $_SERVER['PHP_AUTH_PW'] ?? null,
                ]
            )
            ->setParams(
                array_merge(
                    $_GET,
                    $_POST,
                    json_decode(file_get_contents('php://input'), true) ?? []
                )
            );
    }

    /**
     * @param string $method
     * @return self
     */
    protected function setMethod(string $method): self
    {
        $this->method = $method;
        return $this;
    }

    /**
     * @param string $uri
     * @return self
     */
    protected function setUri(string $uri): self
    {
        $this->uri = $uri;
        return $this;
    }

    /**
     * @param array $params
     * @return self
     */
    protected function setParams(array $params): self
    {
        $this->params = $params;
        return $this;
    }

    /**
     * @param array $auth
     *
     * @return self
     */
    protected function setBasicAuth(array $auth): self
    {
        if (isset($auth['user'], $auth['password'])) {
            $this->auth = $auth;
        }
        return $this;
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
     * @return array
     */
    public function getBasicAuth(): array
    {
        return $this->auth;
    }

    /**
     * @param string $key
     * @param null $default
     *
     * @return mixed|null
     */
    public function get(string $key, $default = null)
    {
        return $this->all()[$key] ?? $default;
    }

    /**
     * @return array
     */
    public function all(): array
    {
        return $this->params;
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($this->params[$key]);
    }

    /**
     * @param array $keys
     *
     * @return array
     */
    public function only(array $keys): array
    {
        $data = [];

        foreach ($this->all() as $key => $value) {
            if (in_array($key, $keys)) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * @param array $keys
     *
     * @return array
     */
    public function except(array $keys): array
    {
        $data = [];

        foreach ($this->all() as $key => $value) {
            if (! in_array($key, $keys)) {
                $data[$key] = $value;
            }
        }

        return $data;
    }

    /**
     * @param array $data
     *
     * @return $this
     */
    public function merge(array $data): self
    {
        $this->params = array_merge($this->params, $data);
        return $this;
    }
}