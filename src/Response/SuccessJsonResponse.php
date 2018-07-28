<?php

namespace App\Response;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class SuccessJsonResponse extends ApiJsonResponse
{
    //https://labs.omniti.com/labs/jsend
    /*public function __construct(?mixed $data = null, int $status = 200, array $headers = array(), bool $json = false)
    {
        parent::__construct($data, $status, $headers, $json);
    }*/

    public function __construct($data = null, ObjectNormalizer $objectNormalizer = null, $statusCode = Response::HTTP_OK)
    {
        parent::__construct(null, $statusCode);

        $serializer = new Serializer([$objectNormalizer], [new JsonEncoder()]);

        $jsonContent = $serializer->serialize(['status' => 'success', 'data' => $data], 'json');

        $this->setContent($jsonContent);
    }
}