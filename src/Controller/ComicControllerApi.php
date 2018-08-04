<?php

namespace App\Controller;

use App\ObjectNormalizer\ProductNormalizer;
use App\Repository\ProductRepository;
use App\Response\ApiJsonResponse;
use App\Response\ErrorJsonResponse;
use App\Response\SuccessJsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/comics")
 */
class ComicControllerApi extends BaseControllerApi
{
    /**
     * @Route("/", name="api_comics", methods="GET")
     *
     * @param ProductRepository $comicRepository
     * @return ApiJsonResponse
     */
    public function index(ProductRepository $comicRepository): ApiJsonResponse
    {
        try {
            $productNormalizer = new ProductNormalizer();

            $serializer = new Serializer([new DateTimeNormalizer(), $productNormalizer]);

            $results = $serializer->normalize($comicRepository->findAll());

            return new SuccessJsonResponse(['offset' => 0, 'limit' => 0, 'total' => count($results), 'count' => count($results), 'results' => $results]);
        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => 'api_comics']);

            return new ErrorJsonResponse('Error in api_comics');
        }
    }
}