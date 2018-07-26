<?php

namespace App\Controller;

use App\Entity\User;
use App\ObjectNormalizer\UserNormalizer;
use App\Repository\UserRepository;
use App\Response\ApiJsonResponse;
use App\Response\ErrorJsonResponse;
use App\Response\SuccessJsonResponse;
use App\Security\UserChecker;
use App\Security\UserProvider;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;


use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/api/auth")
 */
class AuthControllerApi extends BaseControllerApi
{

    /**
     * @Route("/test/", name="api_auth_test", methods="GET")
     *
     * @param UserRepository $userRepository
     * @return ApiJsonResponse
     */
    public function isAuthenticated(UserRepository $userRepository): ApiJsonResponse
    {
        try {

            if (!$this->container->has('security.token_storage')) {
                throw new \LogicException('The SecurityBundle is not registered in your application.');
            }

            if (null === $token = $this->container->get('security.token_storage')->getToken()) {
                return new SuccessJsonResponse(['error']);
            }

            if (!is_object($user = $token->getUser())) {
                // e.g. anonymous authentication
                return new SuccessJsonResponse(['anonymous']);
            }


            return new SuccessJsonResponse($user, new UserNormalizer());

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => 'api_auth_create_admin']);

            return new ErrorJsonResponse('Error in api_auth_create_admin');
        }
    }

    /**
     * @Route("/authenticate/", name="api_auth_authenticate", methods="GET")
     *
     * @param UserRepository $userRepository
     * @return ApiJsonResponse
     *
     * @see https://symfony.com/doc/current/components/security/authentication.html#the-password-encoder-factory
     */
    public function authenticate(UserRepository $userRepository): ApiJsonResponse
    {
        try {

            $user = $userRepository->findOneBy(['username' => 'admin']);


            $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);

            $encoders = [User::class => $defaultEncoder];

            $encoderFactory = new EncoderFactory($encoders);


            $daoProvider = new DaoAuthenticationProvider(new UserProvider(), new UserChecker(), 'secured_area', $encoderFactory);

            $daoProvider->authenticate(new UsernamePasswordToken($user, 'admin', 'secured_area'));






            return new SuccessJsonResponse($user, new UserNormalizer());

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => 'api_auth_create_admin']);

            return new ErrorJsonResponse('Error in api_auth_create_admin');
        }
    }


    /**
     * @Route("/create_admin/", name="api_auth_create_admin", methods="GET")
     *
     * @return ApiJsonResponse
     */
    public function createAdmin(): ApiJsonResponse
    {
        try {

            $user = new User();


            $defaultEncoder = new MessageDigestPasswordEncoder('sha512', true, 5000);

            $encoders = [User::class => $defaultEncoder];

            $encoderFactory = new EncoderFactory($encoders);

            $encoder = $encoderFactory->getEncoder($user);

            $encodedPassword = $encoder->encodePassword('admin', $user->getSalt());


            $user->setUsername('admin');

            $user->setEmail('admin@example.com');

            $user->setPassword($encodedPassword);


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();


            return new SuccessJsonResponse($user, new UserNormalizer());

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => 'api_auth_create_admin']);

            return new ErrorJsonResponse('Error in api_auth_create_admin');
        }
    }
}