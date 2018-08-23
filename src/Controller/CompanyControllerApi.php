<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Response\ApiJsonResponse;
use App\Response\ErrorJsonResponse;
use App\Response\FailureJsonResponse;
use App\Response\SuccessJsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/companies")
 */
class CompanyControllerApi extends BaseControllerApi
{
    /**
     * @Route("/", methods="GET")
     *
     * @param Request $request
     * @param CompanyRepository $companyRepository
     * @return ApiJsonResponse
     */
    public function getCompanies(Request $request, CompanyRepository $companyRepository): ApiJsonResponse
    {
        try {

            $companies = $companyRepository->findAll();

            $results = $this->serializer->normalize($companies, null, ['groups' => ['api:companies:output']]);

            return new SuccessJsonResponse(['offset' => 0, 'limit' => 0, 'total' => count($results), 'count' => count($results), 'results' => $results]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => $request->getPathInfo()]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }

    /**
     * @Route("/", methods="POST")
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param CompanyRepository $companyRepository
     * @return ApiJsonResponse
     */
    public function saveCompanies(Request $request, ValidatorInterface $validator, CompanyRepository $companyRepository): ApiJsonResponse
    {
        try {

            $companyData = $request->get('company');

            $company = null;

            if (isset($companyData['id'])) {
                $company = $companyRepository->findOneBy(['id' => $companyData['id']]);
            } else {
                $company = new Company();
            }

            $this->serializer->denormalize($companyData, 'App\Entity\Company', null,

                ['object_to_populate' => $company, 'groups' => ['api:companies:input']]);


            $errors = $validator->validate($company);

            if ($errors->count()) {

                $this->logger->error((string)$errors, ['route_name' => $request->getPathInfo()]);

                return new FailureJsonResponse(['errors' => $this->serializer->normalize($errors)]);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($company);
            $entityManager->flush();

            return new SuccessJsonResponse();

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => $request->getPathInfo()]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }
}