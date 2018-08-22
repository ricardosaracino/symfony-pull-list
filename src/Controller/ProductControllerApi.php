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
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/products")
 */
class ProductControllerApi extends BaseControllerApi
{
    const CACHE_PREFIX = 'results.product.';

    /**
     * @param array|null $productResults
     * @param SerializerInterface $serializer
     * @param UserPurchaseRepository $purchaseRepository
     */
    private function addUserPurchasesResults(array & $productResults = null, SerializerInterface $serializer, UserPurchaseRepository $purchaseRepository): void
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

            $userPurchaseResults = $serializer->normalize($userPurchases, null, ['groups' => ['api:products:output']]);

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
     * @Route("/{id}", name="api_product", methods={"GET"})
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ProductRepository $repository
     * @param UserPurchaseRepository $purchaseRepository
     * @return ApiJsonResponse
     */
    public function getProduct(Request $request, SerializerInterface $serializer, ProductRepository $repository, UserPurchaseRepository $purchaseRepository): ApiJsonResponse
    {
        try {

            $this->get('cache.app')->deleteItem(self::CACHE_PREFIX . $request->get('id'));

            $cachedResults = $this->get('cache.app')->getItem(self::CACHE_PREFIX . $request->get('id'));

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

                $results = $serializer->normalize($collection, null, ['groups' => ['api:products:output']]);

                ## Cache result

                $cachedResults->set($results);

                $this->get('cache.app')->save($cachedResults);
            }

            ## add un-cached results
            $this->addUserPurchasesResults($results, $serializer, $purchaseRepository);

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
     * @param SerializerInterface $serializer
     * @param ProductRepository $productRepository
     * @param UserPurchaseRepository $purchaseRepository
     * @return ApiJsonResponse
     */
    public function getProducts(Request $request, SerializerInterface $serializer, ProductRepository $productRepository, UserPurchaseRepository $purchaseRepository): ApiJsonResponse
    {
        try {

            $this->get('cache.app')->deleteItem(self::CACHE_PREFIX . base64_encode(md5($request->getQueryString())));

            $cachedResults = $this->get('cache.app')->getItem(self::CACHE_PREFIX . base64_encode(md5($request->getQueryString())));

            if (false || $cachedResults->isHit()) {
                $results = $cachedResults->get();
            } else {

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

                $results = $serializer->normalize($collection, null, ['groups' => ['api:products:output']]);

                ## Cache result

                $cachedResults->set($results);

                $this->get('cache.app')->save($cachedResults);
            }

            ## add un-cached results
            $this->addUserPurchasesResults($results, $serializer, $purchaseRepository);

            return new SuccessJsonResponse(['offset' => count($results), 'limit' => count($results),

                'total' => count($results), 'count' => count($results), 'results' => $results]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => $request->getPathInfo()]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }
}