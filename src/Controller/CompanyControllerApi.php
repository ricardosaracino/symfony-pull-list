<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Response\ApiJsonResponse;
use App\Response\ErrorJsonResponse;
use App\Response\SuccessJsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

/**
 * @Route("/api/companies")
 */
class CompanyControllerApi extends BaseControllerApi
{
    /**
     * @Route("/", name="api_company", methods="GET")
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param CompanyRepository $companyRepository
     * @return ApiJsonResponse
     */
    public function getCompanies(Request $request, SerializerInterface $serializer, CompanyRepository $companyRepository): ApiJsonResponse
    {
        try {

            $companies = $companyRepository->findAll();

            $results = $serializer->normalize($companies, null, ['groups' => ['api:companies:output']]);

            return new SuccessJsonResponse(['offset' => 0, 'limit' => 0, 'total' => count($results), 'count' => count($results), 'results' => $results]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => $request->getPathInfo()]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }

    /**
     * @Route("/", name="api_company", methods="POST")
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param CompanyRepository $companyRepository
     * @return ApiJsonResponse
     */
    public function saveCompanies(Request $request, SerializerInterface $serializer, CompanyRepository $companyRepository): ApiJsonResponse
    {
        try {

            $companyData = $request->get('company');

            $company = null;

            if (array_key_exists('id', $companyData)) {
                $company = $companyRepository->findOneBy(['id' => $companyData['id']]);
            } else {
                $company = new Company();
            }

            $company = $serializer->denormalize($companyData, 'App\Entity\Company', null, ['groups' => ['api:companies:input']]);

            return new SuccessJsonResponse(['offset' => 0, 'limit' => 0, 'total' => 1, 'count' => 1, 'results' => $results]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => $request->getPathInfo()]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }


}