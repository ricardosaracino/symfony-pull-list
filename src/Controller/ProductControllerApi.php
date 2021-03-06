<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Repository\UserPurchaseRepository;
use App\Response\ApiJsonResponse;
use App\Response\ErrorJsonResponse;
use App\Response\SuccessJsonResponse;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api/products")
 */
class ProductControllerApi extends BaseControllerApi
{
    const CACHE_PREFIX = 'results.product.';

    /**
     * @param array|null $productResults
     * @param UserPurchaseRepository $purchaseRepository
     */
    private function addUserPurchasesResults(array & $productResults = null, UserPurchaseRepository $purchaseRepository): void
    {
        if (!empty($productResults)) {

            $productIds = array_map(function ($o) {
                return $o['id'];
            }, $productResults);

            $criteria = Criteria::create();

            $expr = Criteria::expr();

            $criteria->where(
                $expr->andX(
                    $expr->in('product', $productIds),
                    $expr->in('user', [1])
                )
            );

            $userPurchases = $purchaseRepository->matching($criteria);

            $userPurchaseResults = $this->serializer->normalize($userPurchases, null, ['groups' => ['api:products:output']]);

            ## im sure this can be done better
            foreach ($productResults as & $productResult) {

                if (!array_key_exists('userPurchases', $productResult)) {
                    $productResult['userPurchases'] = [];
                }

                foreach ($userPurchaseResults as $userPurchaseResult) {
                    if ($userPurchaseResult['product']['id'] == $productResult['id']) {
                        $productResult['userPurchases'][] = $userPurchaseResult;
                    }
                }
            }
        }
    }

    /**
     * @Route("/{id}", methods={"GET"})
     *
     * @param Request $request
     * @param ProductRepository $repository
     * @param UserPurchaseRepository $purchaseRepository
     * @return ApiJsonResponse
     */
    public function getProduct(Request $request, ProductRepository $repository, UserPurchaseRepository $purchaseRepository): ApiJsonResponse
    {
        try {

            $id = $request->get('id');

            $this->get('cache.app')->deleteItem(self::CACHE_PREFIX . $id);

            $cachedResults = $this->get('cache.app')->getItem(self::CACHE_PREFIX . $id);

            if ($cachedResults->isHit()) {
                $results = $cachedResults->get();
            } else {

                $criteria = Criteria::create();

                $criteria->where(Criteria::expr()->eq('id', $id));

                $collection = $repository->matching($criteria);

                $results = $this->serializer->normalize($collection, null, ['groups' => ['api:products:output']]);

                ## Cache result

                $cachedResults->set($results);

                $this->get('cache.app')->save($cachedResults);
            }

            ## add un-cached results
            $this->addUserPurchasesResults($results, $purchaseRepository);

            return new SuccessJsonResponse(['offset' => 0, 'limit' => 1,

                'total' => 1, 'count' => 1, 'results' => $results]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => $request->getPathInfo()]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }

    /**
     * @Route("/", methods={"GET", "POST"})
     *
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param UserPurchaseRepository $purchaseRepository
     * @return ApiJsonResponse
     */
    public function getProducts(Request $request, ProductRepository $productRepository, UserPurchaseRepository $purchaseRepository): ApiJsonResponse
    {
        try {

            $this->get('cache.app')->deleteItem(self::CACHE_PREFIX . base64_encode(md5($request->getQueryString())));

            $cachedResults = $this->get('cache.app')->getItem(self::CACHE_PREFIX . base64_encode(md5($request->getQueryString())));

            $offset = (int)$request->get('offset');

            $limit = (int)$request->get('limit');


            if (false || $cachedResults->isHit()) {
                $results = $cachedResults->get();
            } else {

                $criteria = Criteria::create();


                if ($offset) {
                    $criteria->setFirstResult($offset);
                }

                if ($limit) {
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

                $results = $this->serializer->normalize($collection, null, ['groups' => ['api:products:output']]);

                ## Cache result

                $cachedResults->set($results);

                $this->get('cache.app')->save($cachedResults);
            }

            ## add un-cached results
            $this->addUserPurchasesResults($results, $purchaseRepository);

            return new SuccessJsonResponse(['offset' => $offset, 'limit' => $limit,

                'total' => count($results), 'count' => count($results), 'results' => $results]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => $request->getPathInfo()]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }
}