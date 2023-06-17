<?php

namespace App\Exceptions;

use App\Facades\Http\Response;

class CantResolveDependenciesException extends \Exception
{
    protected $code = Response::HTTP_INTERNAL_SERVER_ERROR;
}