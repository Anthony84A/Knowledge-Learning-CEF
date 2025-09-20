<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class MailTestController
 *
 * This controller provides a simple route to test email sending
 * using Symfony Mailer. It is mainly intended for development/debug purposes.
 *
 * @package App\Controller
 */
class MailTestController extends AbstractController
{
    /**
     * Sends a test email to verify Symfony Mailer configuration.
     *
     * @param MailerInterface $mailer The mailer service used to send the email.
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse Returns a JSON response confirming the email was sent.
     */
    #[Route('/test-mail', name: 'test_mail')]
    public function sendMail(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('') // TODO: Replace with a valid sender email address
            ->to('')   // TODO: Replace with a valid recipient email address
            ->subject('Test Symfony Mailer')
            ->text('Bonjour, ceci est un test !');

        $mailer->send($email);

        return $this->json(['message' => 'Mail envoyé']);
    }
}
