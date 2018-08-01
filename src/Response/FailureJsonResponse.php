<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;

class FailureJsonResponse extends ApiJsonResponse
{
    //https://labs.omniti.com/labs/jsend
    public function __construct($data = null, $statusCode = Response::HTTP_BAD_REQUEST)
    {
        parent::__construct(['status' => 'fail', 'code' => $statusCode, 'data' => $data], $statusCode);
    }
}