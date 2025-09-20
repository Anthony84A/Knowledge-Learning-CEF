<?php

namespace App\Security;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\CustomUserMessageAuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\CsrfTokenBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\RememberMeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;

/**
 * AppAuthenticator
 *
 * Handles user authentication via a login form.
 *
 * Responsibilities:
 * - Validate user credentials (email and password)
 * - Check if the user is verified
 * - Handle CSRF token validation
 * - Support remember-me functionality
 * - Redirect user after successful authentication
 */
class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    private UrlGeneratorInterface $urlGenerator;
    private UserRepository $userRepository;

    /**
     * Constructor
     *
     * @param UrlGeneratorInterface $urlGenerator Generates URLs for redirects
     * @param UserRepository $userRepository To fetch user data
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, UserRepository $userRepository)
    {
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
    }

    /**
     * Authenticate the user based on email and password from the request.
     *
     * @param Request $request
     * @return Passport
     *
     * @throws UserNotFoundException If user does not exist
     * @throws CustomUserMessageAuthenticationException If user is not verified
     */
    public function authenticate(Request $request): Passport
    {
        $email = $request->request->get('email', '');
        $password = $request->request->get('password', '');
        $csrfToken = $request->request->get('_csrf_token');

        return new Passport(
            new UserBadge($email, function ($userIdentifier) {
                $user = $this->userRepository->findOneBy(['email' => $userIdentifier]);

                if (!$user) {
                    throw new UserNotFoundException(sprintf('User "%s" not found.', $userIdentifier));
                }

                if (!$user->isVerified()) {
                    throw new CustomUserMessageAuthenticationException(
                        'Please confirm your email address before logging in.'
                    );
                }

                return $user;
            }),
            new PasswordCredentials($password),
            [
                new CsrfTokenBadge('authenticate', $csrfToken),
                new RememberMeBadge()
            ]
        );
    }

    /**
     * Called on successful authentication.
     *
     * @param Request $request
     * @param mixed $token
     * @param string $firewallName
     * @return RedirectResponse Redirects the user to home page
     */
    public function onAuthenticationSuccess(Request $request, $token, string $firewallName): RedirectResponse
    {
        return new RedirectResponse($this->urlGenerator->generate('home'));
    }

    /**
     * Returns the login URL.
     *
     * @param Request $request
     * @return string
     */
    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_login');
    }
}
