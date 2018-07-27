<?php

namespace App\Response;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class FailureJsonResponse extends ApiJsonResponse
{
    //https://labs.omniti.com/labs/jsend
    public function __construct($data = null, ObjectNormalizer $objectNormalizer = null)
    {
        parent::__construct();

        $serializer = new Serializer([$objectNormalizer], [new JsonEncoder()]);

        $jsonContent = $serializer->serialize(['status' => 'fail', 'data' => $data], 'json');

        $this->setContent($jsonContent);
    }
}