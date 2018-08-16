<?php

namespace App\Controller;

use App\Entity\User;
use App\ObjectNormalizer\UserNormalizer;
use App\Response\ApiJsonResponse;
use App\Response\ErrorJsonResponse;
use App\Response\SuccessJsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/api/security")
 */
class SecurityControllerApi extends BaseControllerApi
{
    /**
     * @Route("/login", name="api_security_login", methods="POST")
     *
     * @return ApiJsonResponse
     */
    public function login(): ApiJsonResponse
    {
        try {

            $token = $this->get('security.token_storage')->getToken();

            $user = $token->getUser();

            $serializer = new Serializer([new DateTimeNormalizer(), new UserNormalizer()]);

            $results = $serializer->normalize($user);

            return new SuccessJsonResponse(['results' => $results]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => 'api_auth_create_admin']);

            return new ErrorJsonResponse('Error in api_auth_create_admin');
        }
    }

    /**
     * @Route("/logout", name="api_security_logout", methods="GET")
     *
     * @return ApiJsonResponse
     *
     * @see \App\Security\LogoutSuccessHandler
     */
    public function logout(): ApiJsonResponse
    {
        ## Needed for Route, LogoutSuccessHandler is called instead
        return new SuccessJsonResponse();
    }

    /**
     * @Route("/user", name="api_security_user", methods="GET")
     */
    public function user(): ApiJsonResponse
    {
        try {

            $token = $this->get('security.token_storage')->getToken();

            if ($token->isAuthenticated()) {
                // "anon." is authenticated when firewalls: api: anonymous: ~
                return new SuccessJsonResponse($token->getUser(), new UserNormalizer());
            }

            return new ErrorJsonResponse('Authentication Required', Response::HTTP_UNAUTHORIZED);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => 'api_security_login']);

            return new ErrorJsonResponse('Error in api_security_login');
        }
    }


    /**
     * @Route("/signup", name="api_security_signup", methods="GET")
     */
    public function signup(\Swift_Mailer $mailer): ApiJsonResponse
    {
        try {

            $message = (new \Swift_Message('Verify your email address'))
                ->setFrom('test@pullist.com')
                ->setTo('recipient@example.com')
                ->setBody(
                    $this->renderView('emails/signup.html.twig', [
                        'base' => 'http://localhost:4200/email-verification',
                        'token' => md5('')
                    ]),
                    'text/html'
                );

            $result = $mailer->send($message);

            return new SuccessJsonResponse(['results' => $result]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => 'api_security_login']);

            return new ErrorJsonResponse('Error in api_security_login');
        }
    }


    /**
     * @Route("/create_admin", name="api_security_create_admin", methods="GET")
     *
     * @return ApiJsonResponse
     */
    public function createAdmin(): ApiJsonResponse
    {
        try {

            $user = new User();

            $encoder = new MessageDigestPasswordEncoder('sha512', true, 5000);

            $encodedPassword = $encoder->encodePassword('admin', $user->getSalt());

            $user->setUsername('admin');

            $user->setPassword($encodedPassword);

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return new SuccessJsonResponse($user, new UserNormalizer());

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => 'api_security_create_admin']);

            return new ErrorJsonResponse('Error in api_security_create_admin');
        }
    }
}