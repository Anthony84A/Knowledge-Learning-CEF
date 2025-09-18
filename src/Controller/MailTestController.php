<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class MailTestController extends AbstractController
{
    #[Route('/test-mail', name: 'test_mail')]
    public function sendMail(MailerInterface $mailer)
    {
        $email = (new Email())
            ->from('anthony.arniaud@gmail.com')
            ->to('anthony.play84@gmail.com')
            ->subject('Test Symfony Mailer')
            ->text('Bonjour, ceci est un test !');

        $mailer->send($email);

        return $this->json(['message' => 'Mail envoyé']);
    }
}
