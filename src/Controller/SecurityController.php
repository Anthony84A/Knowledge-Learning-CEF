<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Controller responsible for user authentication.
 *
 * Handles:
 * - User login
 * - User logout
 */
class SecurityController extends AbstractController
{
    /**
     * Displays the login form and handles login errors.
     *
     * @param AuthenticationUtils $authenticationUtils Service to handle login errors and retrieve last username
     *
     * @return Response Renders the login page with last entered username and any authentication error
     */
    #[Route('/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        return $this->render('security/login.html.twig', [
            'last_username' => $authenticationUtils->getLastUsername(),
            'error' => $authenticationUtils->getLastAuthenticationError(),
        ]);
    }

    /**
     * Handles user logout.
     *
     * This method is only used as a route endpoint; the actual logout is managed by Symfony security.
     *
     * @return void
     */
    #[Route('/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Symfony will handle the logout automatically
    }
}
