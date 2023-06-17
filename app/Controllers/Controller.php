<?php

namespace App\Controllers;

use App\Facades\Http\Request;

class Controller
{
    /** @var Request */
    protected $request;

    /**
     * @return $this
     */
    public function authorize(): self
    {
        // Some authorization logic
        return $this;
    }

    /**
     * Validate the request.
     *
     * @return $this
     */
    public function validate(): self
    {
        // Some validation logic
        return $this;
    }

    public function setRequest(Request $request): self
    {
        $this->request = $request;
        return $this;
    }
}