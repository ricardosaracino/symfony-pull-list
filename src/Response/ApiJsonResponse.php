<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiJsonResponse extends JsonResponse
{
    //https://labs.omniti.com/labs/jsend
    public function __construct($data = null, int $statusCode = 200, array $headers = array(), bool $json = false)
    {
        parent::__construct($data, $statusCode, $headers + ['Access-Control-Allow-Origin' => '*'], $json);
    }
}