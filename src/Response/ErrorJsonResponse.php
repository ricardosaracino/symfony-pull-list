<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;

class ErrorJsonResponse extends ApiJsonResponse
{
    //https://labs.omniti.com/labs/jsend

    public function __construct(string $message = null, $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR)
    {
        parent::__construct(['status' => 'error', 'code' => $statusCode, 'message' => $message], $statusCode);
    }
}