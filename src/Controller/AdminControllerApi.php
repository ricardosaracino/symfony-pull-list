<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use App\Response\ApiJsonResponse;
use App\Response\ErrorJsonResponse;
use App\Response\FailureJsonResponse;
use App\Response\SuccessJsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/admin")
 *
 * @Security("has_role('ROLE_ADMIN')")
 */
class AdminControllerApi extends BaseControllerApi
{
    /**
     * @Route("/companies", methods="GET")
     *
     * @param Request $request
     * @param CompanyRepository $companyRepository
     * @return ApiJsonResponse
     */
    public function getCompanies(Request $request, CompanyRepository $companyRepository): ApiJsonResponse
    {
        try {

            $companies = $companyRepository->findAll();

            $results = $this->serializer->normalize($companies, null, ['groups' => ['api:companies:output', 'timestampable']]);

            return new SuccessJsonResponse(['offset' => 0, 'limit' => 0, 'total' => count($results), 'count' => count($results), 'results' => $results]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => $request->getPathInfo()]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }


    /**
     * @Route("/companies/{id}", methods="GET")
     *
     * @param Request $request
     * @param CompanyRepository $companyRepository
     * @return ApiJsonResponse
     */
    public function getCompany(Request $request, CompanyRepository $companyRepository): ApiJsonResponse
    {
        try {

            $company = $companyRepository->find($request->get('id'));

            $results = $this->serializer->normalize($company, null,

                ['groups' => ['api:companies:output', 'timestampable']]);

            return new SuccessJsonResponse(['offset' => 0, 'limit' => 1,

                'total' => 1, 'count' => 1, 'results' => [$results]]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => $request->getPathInfo()]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }

    /**
     * @Route("/companies", methods="POST")
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

            return new SuccessJsonResponse(null, Response::HTTP_CREATED);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => $request->getPathInfo()]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }
}