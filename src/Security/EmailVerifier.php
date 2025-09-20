<?php

namespace App\Security;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

/**
 * EmailVerifier
 *
 * Handles the generation, sending, and validation of email verification links for users.
 *
 * Responsibilities:
 * - Generate signed email confirmation URLs
 * - Send confirmation emails to users
 * - Validate confirmation requests and mark users as verified
 */
class EmailVerifier
{
    private VerifyEmailHelperInterface $verifyEmailHelper;
    private MailerInterface $mailer;
    private EntityManagerInterface $entityManager;

    /**
     * Constructor.
     *
     * @param VerifyEmailHelperInterface $verifyEmailHelper Helper to generate and validate email signatures
     * @param MailerInterface $mailer Mailer service to send emails
     * @param EntityManagerInterface $entityManager Doctrine entity manager
     */
    public function __construct(
        VerifyEmailHelperInterface $verifyEmailHelper,
        MailerInterface $mailer,
        EntityManagerInterface $entityManager
    ) {
        $this->verifyEmailHelper = $verifyEmailHelper;
        $this->mailer = $mailer;
        $this->entityManager = $entityManager;
    }

    /**
     * Send an email confirmation link to the user.
     *
     * @param string $verifyEmailRouteName Name of the route to verify the email
     * @param User $user The user to verify
     * @param TemplatedEmail $email The email object to send
     */
    public function sendEmailConfirmation(string $verifyEmailRouteName, User $user, TemplatedEmail $email): void
    {
        $signatureComponents = $this->verifyEmailHelper->generateSignature(
            $verifyEmailRouteName,
            (string) $user->getId(),
            (string) $user->getEmail()
        );

        $context = $email->getContext();
        $context['signedUrl'] = $signatureComponents->getSignedUrl();
        $context['expiresAtMessageKey'] = $signatureComponents->getExpirationMessageKey();
        $context['expiresAtMessageData'] = $signatureComponents->getExpirationMessageData();

        $email->context($context);

        $this->mailer->send($email);
    }

    /**
     * Validate the email confirmation from the request and mark the user as verified.
     *
     * @param Request $request
     * @param User $user
     *
     * @throws VerifyEmailExceptionInterface If the signature is invalid or expired
     */
    public function handleEmailConfirmation(Request $request, User $user): void
    {
        $this->verifyEmailHelper->validateEmailConfirmationFromRequest(
            $request,
            (string) $user->getId(),
            (string) $user->getEmail()
        );

        $user->setIsVerified(true);

        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }
}
