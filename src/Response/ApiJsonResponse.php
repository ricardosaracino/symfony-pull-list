<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\JsonResponse;

class ApiJsonResponse extends JsonResponse
{
    //https://labs.omniti.com/labs/jsend
    public function __construct($data = null, int $statusCode = 200, array $headers = array(), bool $json = false)
    {
        parent::__construct($data, $statusCode,

            ## https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS#Requests_with_credentials
            $headers +[

                'Access-Control-Allow-Origin' => 'http://localhost:4200',

                'Access-Control-Allow-Credentials' => 'true',

                'Access-Control-Allow-Methods' => 'GET,POST,DELETE,OPTIONS,PUT',

                'AccessControlAllowHeaders' => 'Content-Type, *',

            ] , $json);
    }
}