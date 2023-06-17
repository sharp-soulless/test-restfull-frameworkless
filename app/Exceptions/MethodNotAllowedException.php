<?php

namespace App\Exceptions;

use App\Facades\Http\Response;

class MethodNotAllowedException extends \Exception
{
    protected $code = Response::HTTP_METHOD_NOT_ALLOWED;
}