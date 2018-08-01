<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;

class SuccessJsonResponse extends ApiJsonResponse
{
    //https://labs.omniti.com/labs/jsend
    public function __construct($data = null, $statusCode = Response::HTTP_OK)
    {
        parent::__construct(['status' => 'success', 'code' => $statusCode, 'data' => $data], $statusCode);
    }
}