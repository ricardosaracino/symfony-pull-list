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
use Symfony\Component\Serializer\SerializerInterface;
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
     * @Route("/", methods="POST")
     *
     * @param Request $request
     * @param SerializerInterface $serializer
     * @param ValidatorInterface $validator
     * @param CompanyRepository $companyRepository
     * @return ApiJsonResponse
     */
    public function saveCompanies(Request $request, SerializerInterface $serializer, ValidatorInterface $validator, CompanyRepository $companyRepository): ApiJsonResponse
    {
        try {

            $companyData = $request->get('company');

            $company = null;

            if (isset($companyData['id'])) {
                $company = $companyRepository->findOneBy(['id' => $companyData['id']]);
            } else {
                $company = new Company();
            }

            $serializer->denormalize($companyData, 'App\Entity\Company', null,

                ['object_to_populate' => $company, 'groups' => ['api:companies:input']]);


            $errors = $validator->validate($company);

            if ($errors->count()) {

                $this->logger->error((string)$errors, ['route_name' => $request->getPathInfo()]);

                return new FailureJsonResponse(['errors' => $serializer->normalize($errors)]);
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