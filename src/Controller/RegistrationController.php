<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

/**
 * Controller responsible for user registration and email verification.
 *
 * Handles:
 * - User account creation
 * - Password hashing
 * - Sending confirmation emails
 * - Automatic login after registration
 * - Email verification
 */
class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    /**
     * Constructor.
     *
     * @param EmailVerifier $emailVerifier Service to send and verify email confirmations
     */
    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * Handles user registration.
     *
     * @param Request $request The HTTP request
     * @param UserPasswordHasherInterface $userPasswordHasher Service to hash passwords
     * @param Security $security Service to log the user in automatically
     * @param EntityManagerInterface $entityManager Service to persist the user entity
     *
     * @return Response Returns the registration form or redirects after registration
     */
    #[Route('/register', name: 'app_register')]
    public function register(
        Request $request,
        UserPasswordHasherInterface $userPasswordHasher,
        Security $security,
        EntityManagerInterface $entityManager
    ): Response {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hash the plain password and set it on the user entity
            $plainPassword = $form->get('plainPassword')->getData();
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Persist the new user to the database
            $entityManager->persist($user);
            $entityManager->flush();

            // Send email confirmation with signed URL
            $this->emailVerifier->sendEmailConfirmation(
                'app_verify_email',
                $user,
                (new TemplatedEmail())
                    ->from(new Address('anthony.arniaud@gmail.com', 'Knowledge Learning'))
                    ->to((string) $user->getEmail())
                    ->subject('Confirm your email address')
                    ->htmlTemplate('registration/confirmation_email.html.twig')
            );

            // Flash message to inform user
            $this->addFlash('success', 'Account created successfully. Please check your email to confirm registration.');

            // Automatically log the user in
            return $security->login($user, AppAuthenticator::class, 'main');
        }

        // Render registration form if not submitted or invalid
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    /**
     * Verifies user's email after clicking the confirmation link.
     *
     * @param Request $request The HTTP request
     * @param TranslatorInterface $translator Service to translate messages
     *
     * @return Response Redirects the user after verification
     */
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        // Ensure user is authenticated
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        try {
            $user = $this->getUser();
            $this->emailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            // Show error message if verification fails
            $this->addFlash('error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register');
        }

        // Show success message
        $this->addFlash('success', 'Your email address has been verified successfully ✅');

        return $this->redirectToRoute('home');
    }
}
