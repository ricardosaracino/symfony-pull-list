<?php


namespace App\Security;

use App\Response\ErrorJsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Guard\AbstractGuardAuthenticator;


/**
 * Class UserAuthenticator
 * @package App\Security
 *
 * @see https://symfony.com/doc/current/security/guard_authentication.html
 * @see https://symfony.com/doc/current/components/security/authentication.html
 */
class ApiLoginAuthenticator extends AbstractGuardAuthenticator
{
    /**
     * {@inheritdoc}
     */
    public function supports(Request $request)
    {
        if ($request->getPathInfo() != '/api/security/login' || !$request->isMethod('POST')) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function getCredentials(Request $request)
    {
        try {
            ## todo better way to do this?
            return json_decode($request->getContent());
        } catch (\Exception $exception) {

        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function getUser($credentials, UserProviderInterface $userProvider)
    {
        $username = $credentials->username;

        if (null === $username) {
            return null;
        }

        return $userProvider->loadUserByUsername($username);
    }

    /**
     * {@inheritdoc}
     */
    public function checkCredentials($credentials, UserInterface $user)
    {
        $password = $credentials->password;

        if (null === $password) {
            return null;
        }

        $encoder = new MessageDigestPasswordEncoder('sha512', true, 5000);

        if (!$encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {

            # Calls \App\Security\ApiLoginAuthenticator::onAuthenticationFailure
            throw new AuthenticationException('Invalid Password');
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token, $providerKey)
    {
        // If the firewall name is not main, then the set value would be instead:

        // on success, let the request continue
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $authException)
    {
        return new ErrorJsonResponse($authException->getMessage(), Response::HTTP_FORBIDDEN);
    }

    /**
     * Called when authentication is needed, but it's not sent
     *
     * Set firewall: anonymous: false
     *
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        return new ErrorJsonResponse('Authentication Required', Response::HTTP_UNAUTHORIZED);
    }

    /**
     * {@inheritdoc}
     */
    public function supportsRememberMe()
    {
        return false;
    }
}