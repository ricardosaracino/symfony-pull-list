<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Response\ApiJsonResponse;
use App\Response\ErrorJsonResponse;
use App\Response\FailureJsonResponse;
use App\Response\SuccessJsonResponse;
use Doctrine\Common\Collections\Criteria;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Guard\Token\PostAuthenticationGuardToken;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/security")
 */
class SecurityControllerApi extends BaseControllerApi
{
    /**
     * @Route("/login", methods="POST")
     *
     * @param Request $request
     * @return ApiJsonResponse
     */
    public function login(Request $request): ApiJsonResponse
    {
        try {

            $token = $this->get('security.token_storage')->getToken();

            $user = $this->serializer->normalize($token->getUser(), null, ['groups' => ['token']]);

            return new SuccessJsonResponse(['user' => $user]);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => ['route_name' => $request->getPathInfo()]]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }

    /**
     * @Route("/logout", methods="DELETE")
     *
     * @see \App\Security\LogoutSuccessHandler
     *
     * @param Request $request
     * @return ApiJsonResponse
     */
    public function logout(Request $request): ApiJsonResponse
    {
        ## Needed for Route, LogoutSuccessHandler is called instead
        return new SuccessJsonResponse();
    }

    /**
     * @Route("/user", methods="GET")
     *
     * @param Request $request
     * @return ApiJsonResponse
     */
    public function user(Request $request): ApiJsonResponse
    {
        try {

            $token = $this->get('security.token_storage')->getToken();

            if ($token->isAuthenticated()) {
                // "anon." is authenticated when firewalls: api: anonymous: ~

                $user = $this->serializer->normalize($token->getUser(), null, ['groups' => ['token']]);

                return new SuccessJsonResponse(['user' => $user]);
            }

            return new ErrorJsonResponse('Authentication Required', Response::HTTP_UNAUTHORIZED);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => ['route_name' => $request->getPathInfo()]]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }

    /**
     * @Route("/register", methods={"POST"})
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param \Swift_Mailer $mailer
     * @return ApiJsonResponse
     */
    public function register(Request $request, ValidatorInterface $validator, \Swift_Mailer $mailer): ApiJsonResponse
    {
        try {

            if (!$email = $request->get('email')) {
                throw new \Exception('Requires email');
            }

            if (!$password = $request->get('password')) {
                throw new \Exception('Requires password');
            }

            if (!$redirectUrl = $request->get('redirectUrl')) {
                throw new \Exception('Requires redirectUrl');
            }

            $token = bin2hex(random_bytes(32));


            $user = new User();

            $user->setUsername($email); // TODO would need to make a custom UserProviderInterface for email
            $user->setEmail($email);
            $user->setTextPassword($password); // Not saved just for validation
            $user->setIsActive(false);
            $user->setRegistrationVerificationToken($token);
            $user->setRegistrationVerificationTokenExpiresAt(new \DateTime('+24 hours'));


            $user->setSalt(bin2hex(random_bytes(16)));

            $encoder = new MessageDigestPasswordEncoder('sha512', true, 5000);

            $encodedPassword = $encoder->encodePassword($password, $user->getSalt());

            $user->setPassword($encodedPassword);


            $errors = $validator->validate($user, null, ['register']);

            if ($errors->count()) {
                return new FailureJsonResponse(['errors' => $this->serializer->normalize($errors)]);
            }

            $message = (new \Swift_Message('Verify your email address to complete signup'))
                ->setFrom('from@example.com')
                ->setTo($email)
                ->setBody(
                    $this->renderView('emails/registration.html.twig', [
                        'base' => $redirectUrl,
                        'token' => $token
                    ]),
                    'text/html'
                );

            if ($result = $mailer->send($message)) {
                return new FailureJsonResponse(['errors' => ['message' => 'Error sending mail']]);
            }

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return new SuccessJsonResponse(null, Response::HTTP_CREATED);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => ['route_name' => $request->getPathInfo()]]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }

    /**
     * @Route("/verify_registration", methods={"OPTIONS", "POST"})
     *
     * @param Request $request
     * @param UserRepository $userRepository
     * @return ApiJsonResponse
     */
    public function verifyRegistration(Request $request, UserRepository $userRepository): ApiJsonResponse
    {
        try {

            if (!$token = $request->get('token')) {
                throw new \Exception('Requires token');
            }

            $criteria = Criteria::create();

            $criteria->where(Criteria::expr()->eq('registrationVerificationToken', $token));

            $criteria->where(Criteria::expr()->gt('registrationVerificationTokenExpiresAt', new \DateTime('now', new \DateTimeZone('UTC'))));

            /** @var User $user */
            $user = $userRepository->matching($criteria)->first();

            if (!$user) {
                return new FailureJsonResponse();
            }

            $user->setIsActive(true);
            $user->setRegistrationVerificationToken(null);
            $user->setRegistrationVerificationTokenExpiresAt(null);
            $user->setRegistrationVerifiedAt(new \DateTime());

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return new SuccessJsonResponse();

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => ['route_name' => $request->getPathInfo()]]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }

    /**
     * @Route("/create_admin", methods="GET")
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

            return new SuccessJsonResponse($user);

        } catch (\Exception $exception) {

            $this->logger->error($exception->getMessage(), ['route_name' => ['route_name' => $request->getPathInfo()]]);

            return new ErrorJsonResponse('Error in ' . $request->getPathInfo());
        }
    }
}