<?php

namespace App\Exceptions;

use App\Facades\Http\Response;

class UndefinedMethodException extends \Exception
{
    protected $code = Response::HTTP_INTERNAL_SERVER_ERROR;
}