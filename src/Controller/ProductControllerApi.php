<?php

namespace App\Controller;

use App\ObjectNormalizer\ProductNormalizer;
use App\Repository\ProductRepository;
use App\Response\ApiJsonResponse;
use App\Response\ErrorJsonResponse;
use App\Response\SuccessJsonResponse;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/products")
 */
class ProductControllerApi extends BaseControllerApi
{
    /**
     * @Route("/", name="api_products", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param ProductRepository $repository
     * @return ApiJsonResponse
     */
    public function getProducts(Request $request, ProductRepository $repository): ApiJsonResponse
    {
        try {

            $criteria = Criteria::create();

            if($offset = $request->get('offset')){
                $criteria->setFirstResult($offset);
            }

            if($limit = $request->get('limit')){
                $criteria->setMaxResults($limit);
            }

            if($orderBy = $request->get('orderBy')){
                // TODO $criteria->orderBy([$orderBy]);
            }

            if($dateRange = $request->get('dateRange')) {

                list($start, $end) = explode(',', $dateRange);

                $expr = Criteria::expr();
                $criteria->where(
                    $expr->andX(
                        $expr->gte('releasedAt', new \DateTime($start)),
                        $expr->lte('releasedAt', new \DateTime($end))
                    )
                );
            }

            $productNormalizer = new ProductNormalizer();

            $serializer = new Serializer([new DateTimeNormalizer(), $productNormalizer]);

            $collection = $repository->matching($criteria);

            $results = $serializer->normalize($collection);

            return new SuccessJsonResponse(['offset' => intval($offset), 'limit' => intval($limit), 'total' => $collection->count(), 'count' => $collection->count(), 'results' => $results]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => $request->getPathInfo()]);

            return new ErrorJsonResponse('Error in '. $request->getPathInfo());
        }
    }
}