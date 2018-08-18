<?php

namespace App\Controller;

use App\Entity\User;
use App\ObjectNormalizer\UserNormalizer;
use App\Repository\UserRepository;
use App\Response\ApiJsonResponse;
use App\Response\ErrorJsonResponse;
use App\Response\SuccessJsonResponse;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;
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
     * @Route("/signup", name="api_security_signup", methods={"OPTIONS", "POST"})
     *
     * @param Request $request
     * @param \Swift_Mailer $mailer
     * @return ApiJsonResponse
     */
    public function signup(Request $request, \Swift_Mailer $mailer): ApiJsonResponse
    {
        try {

            if (!$email = $request->get('email')) {
                throw new \Exception('Requires email');
            }

            if (!$redirectUrl = $request->get('redirectUrl')) {
                throw new \Exception('Requires redirectUrl');
            }

            $token = bin2hex(random_bytes(32));

            $message = (new \Swift_Message('Verify your email address'))
                ->setFrom($email)
                ->setTo('recipient@example.com')
                ->setBody(
                    $this->renderView('emails/signup.html.twig', [
                        'base' => $redirectUrl,
                        'token' => $token
                    ]),
                    'text/html'
                );

            $result = $mailer->send($message);


            $user = new User();
            $user->setIsActive(false);

            // TODO do we set the password, allow them to be null, do i need a username?
            $user->setPassword(substr(md5($email), 0, 21));
            $user->setUsername(substr(md5($email), 0, 21));

            $user->setEmail($email);
            $user->setEmailVerificationToken($token);
            $user->setEmailVerificationTokenExpiresAt(new \DateTime('+24 hours'));

            $entityManager = $this->getDoctrine()->getManager();

            $entityManager->persist($user);

            $entityManager->flush();

            return new SuccessJsonResponse(['results' => ['mailer_send' => $result]]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => ['route_name' => $request->getPathInfo()]]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }

    /**
     * @Route("/verify_email", name="api_security_verify_email", methods={"OPTIONS", "POST"})
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @return ApiJsonResponse
     */
    public function verifyEmailToken(Request $request, UserRepository $userRepository): ApiJsonResponse
    {
        try {

            if (!$emailVerificationToken = $request->get('emailVerificationToken')) {
                throw new \Exception('Requires emailVerificationToken');
            }

            $criteria = Criteria::create();

            $criteria->where(Criteria::expr()->eq('emailVerificationToken', $emailVerificationToken));

            $criteria->where(Criteria::expr()->gt('emailVerificationTokenExpiresAt', new \DateTime('now', new \DateTimeZone('UTC'))));

            $user = $userRepository->matching($criteria)->first();

            $user->setIsActive(true);
            $user->setEmailVerificationToken(null);
            $user->setEmailVerificationTokenExpiresAt(null);
            $user->setEmailVerifiedAt(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return new SuccessJsonResponse(['results' => ['mailer_send' => 1]]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => ['route_name' => $request->getPathInfo()]]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }

    /**
     * @Route("/create_admin", name="api_security_create_admin", methods="GET")
     *
     * @param Request $request
     * @return ApiJsonResponse
     */
    public function createAdmin(Request $request): ApiJsonResponse
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

            $this->logger->error($exception->getMessage(), ['route_name' => ['route_name' => $request->getPathInfo()]]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }
}