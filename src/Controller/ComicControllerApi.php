<?php

namespace App\Controller;

use App\ObjectNormalizer\ProductNormalizer;
use App\Repository\ProductRepository;
use App\Response\ApiJsonResponse;
use App\Response\ErrorJsonResponse;
use App\Response\SuccessJsonResponse;
use Symfony\Component\Routing\Annotation\Route;

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
			return new SuccessJsonResponse($comicRepository->findAll(), new ProductNormalizer());
		} catch (\Exception $exception) {

			$this->logger->error($exception->getMessage(),['route_name' => 'api_comics']);

			return new ErrorJsonResponse('Error in api_comics');
		}
	}
}