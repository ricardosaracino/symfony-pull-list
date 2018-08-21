<?php

namespace App\Controller;

use App\Repository\CompanyRepository;
use App\Response\ApiJsonResponse;
use App\Response\ErrorJsonResponse;
use App\Response\SuccessJsonResponse;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/companies")
 */
class CompanyControllerApi extends BaseControllerApi
{
    /**
     * @Route("/", name="api_company", methods="GET")
     *
     * @param Request $request
     * @param CompanyRepository $repository
     * @return ApiJsonResponse
     */
    public function getCompanies(Request $request, CompanyRepository $repository): ApiJsonResponse
    {
        try {

            $companies = $repository->findAll();

            $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));

            $normalizer = new ObjectNormalizer($classMetadataFactory);

            $serializer = new Serializer([new DateTimeNormalizer(), $normalizer]);

            $results = $serializer->normalize($companies, null, ['groups' => ['api:companies:output']]);

            return new SuccessJsonResponse(['offset' => 0, 'limit' => 0, 'total' => count($results), 'count' => count($results), 'results' => $results]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => $request->getPathInfo()]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }
}