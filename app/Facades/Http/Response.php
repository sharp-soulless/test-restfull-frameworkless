<?php

namespace App\Facades\Http;

class Response
{
    const HTTP_CONTINUE = 100;
    const HTTP_SWITCHING_PROTOCOLS = 101;

    const HTTP_OK = 200;
    const HTTP_CREATED = 201;
    const HTTP_ACCEPTED = 202;
    const HTTP_NO_CONTENT = 204;

    const HTTP_MULTIPLE_CHOICES = 300;
    const HTTP_MOVED_PERMANENTLY = 301;
    const HTTP_FOUND = 302;

    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_PAYMENT_REQUIRED = 402;
    const HTTP_FORBIDDEN = 403;
    const HTTP_NOT_FOUND = 404;
    const HTTP_METHOD_NOT_ALLOWED = 405;

    const HTTP_INTERNAL_SERVER_ERROR = 500;
    const HTTP_NOT_IMPLEMENTED = 501;
    const HTTP_BAD_GATEWAY = 502;
    const HTTP_SERVICE_UNAVAILABLE = 503;

    const CONTENT_TYPE_JSON = 'Content-type: application/json;';

    /** @var array */
    protected $data;

    /** @var int  */
    protected $statusCode;

    /** @var array  */
    protected $headers;

    /**
     * @param array $data
     * @param int $statusCode
     * @param array $headers
     */
    public function __construct(array $data, int $statusCode = 200, array $headers = [])
    {
        $this->data = $data;
        $this->statusCode = $this->validateStatusCode($statusCode);
        $this->headers = $headers;
    }

    /**
     * @param int $code
     *
     * @return int
     */
    protected function validateStatusCode(int $code): int
    {
        if ($code < 100 || $code > 599) {
            throw new \InvalidArgumentException('Invalid status code');
        }

        return $code;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        http_response_code($this->statusCode);

        foreach ($this->headers as $header) {
            header($header);
        }

        echo ($response = json_encode($this->data));
        return $response;
    }
}