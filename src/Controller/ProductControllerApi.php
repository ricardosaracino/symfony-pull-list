<?php

namespace App\Controller;

use App\ObjectNormalizer\EntityNormalize;
use App\ObjectNormalizer\ProductNormalizer;
use App\Repository\ProductRepository;
use App\Repository\UserPurchaseRepository;
use App\Response\ApiJsonResponse;
use App\Response\ErrorJsonResponse;
use App\Response\SuccessJsonResponse;
use Doctrine\Common\Collections\Collection;
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
     * @param Collection $products
     * @param UserPurchaseRepository $purchaseRepository
     * @return array
     */
    private function getUserPurchasesResults(Collection $products, UserPurchaseRepository $purchaseRepository)
    {
        if ($products->count()) {

            $productIds = $products->map(function ($o) {
                return $o->getId();
            })->toArray();

            $criteria = Criteria::create();

            $criteria->where(Criteria::expr()->in('product', $productIds));

            $userPurchaseNormalizer = new EntityNormalize();

            $userPurchaseNormalizer->setIgnoredAttributes(['id', 'user']);

            $userPurchaseNormalizer->setCallbacks(array('product' => function ($o) {
                return ['id' => $o->getId()];
            }));

            $serializer = new Serializer([new DateTimeNormalizer(), $userPurchaseNormalizer]);

            $userPurchases = $purchaseRepository->matching($criteria);

            return $serializer->normalize($userPurchases);
        }

        return [];
    }

    /**
     * @Route("/{id}", name="api_product", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param ProductRepository $repository
     * @return ApiJsonResponse
     */
    public function getCachedProduct(Request $request, ProductRepository $repository,  UserPurchaseRepository $purchaseRepository): ApiJsonResponse
    {
        try {

            $cachedResults = $this->get('cache.app')->getItem('results_products_' . $request->get('id'));

            if ($cachedResults->isHit()) {
                $results = $cachedResults->get();
            } else {

                $criteria = Criteria::create();

                if ($id = $request->get('id')) {
                    $criteria->where(Criteria::expr()->eq('id', $id));
                } else {
                    $criteria->where(Criteria::expr()->eq('id', 0));
                }

                $collection = $repository->matching($criteria);

                $serializer = new Serializer([new DateTimeNormalizer(), new ProductNormalizer()]);

                $results = $serializer->normalize($collection);

                ## Cache result

                $cachedResults->set($results);

                $this->get('cache.app')->save($cachedResults);
            }

            ## todo refactor to use results
            ##$userPurchasesResults = $this->getUserPurchasesResults($collection, $purchaseRepository);

            return new SuccessJsonResponse(['offset' => count($results), 'limit' => count($results),

                'total' => count($results), 'count' => count($results), 'results' => $results]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => $request->getPathInfo()]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }

    /**
     * @Route("/", name="api_products", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param UserPurchaseRepository $purchaseRepository
     * @return ApiJsonResponse
     */
    public function getProducts(Request $request, ProductRepository $productRepository, UserPurchaseRepository $purchaseRepository): ApiJsonResponse
    {
        try {

            $criteria = Criteria::create();

            if ($offset = $request->get('offset')) {
                $criteria->setFirstResult($offset);
            }

            if ($limit = $request->get('limit')) {
                $criteria->setMaxResults($limit);
            }

            if ($orderBy = $request->get('orderBy')) {
                // TODO $criteria->orderBy([$orderBy]);
            }

            if ($dateRange = $request->get('dateRange')) {

                list($start, $end) = explode(',', $dateRange);

                $expr = Criteria::expr();
                $criteria->where(
                    $expr->andX(
                        $expr->gte('releasedAt', new \DateTime($start)),
                        $expr->lte('releasedAt', new \DateTime($end))
                    )
                );
            }

            $collection = $productRepository->matching($criteria);

            $serializer = new Serializer([new DateTimeNormalizer(), new ProductNormalizer()]);

            $results = $serializer->normalize($collection);

            $userPurchasesResults = $this->getUserPurchasesResults($collection, $purchaseRepository);

            return new SuccessJsonResponse(['offset' => intval($offset), 'limit' => intval($limit),

                'total' => $collection->count(), 'count' => $collection->count(),

                'results' => $results, 'purchase_results' => $userPurchasesResults]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => $request->getPathInfo()]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }
}