<?php

namespace App\Controller;

use App\ObjectNormalizer\ProductNormalizer;
use App\Repository\CompanyRepository;
use App\Repository\ProductRepository;
use App\Response\ApiJsonResponse;
use App\Response\ErrorJsonResponse;
use App\Response\SuccessJsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/company")
 */
class CompanyControllerApi extends BaseControllerApi
{
    /**
     * @Route("/", name="api_company", methods="GET")
     *
     * @param CompanyRepository $repository
     * @return ApiJsonResponse
     */
    public function getCompanies(CompanyRepository $repository): ApiJsonResponse
    {
        try {
            $productNormalizer = new ProductNormalizer();

            $serializer = new Serializer([new DateTimeNormalizer(), $productNormalizer]);

            $results = $serializer->normalize($repository->findAll());

            return new SuccessJsonResponse(['offset' => 0, 'limit' => 0, 'total' => count($results), 'count' => count($results), 'results' => $results]);
        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => 'api_company']);

            return new ErrorJsonResponse('Error in api_company');
        }
    }
}