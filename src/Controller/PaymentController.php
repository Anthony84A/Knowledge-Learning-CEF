<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\Purchase;
use App\Entity\Cursus;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class PaymentController extends AbstractController
{
    #[Route('/checkout/{id}', name: 'payment_checkout')]
    public function checkout(Lesson $lesson, Request $request): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $YOUR_DOMAIN = $request->getSchemeAndHttpHost();

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => intval($lesson->getPrice() * 100), // prix en centimes
                    'product_data' => [
                        'name' => $lesson->getTitle(),
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success/' . $lesson->getId() . '/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/cancel',
        ]);

        return $this->redirect($checkout_session->url);
    }

    #[Route('/success/{lessonId}/{sessionId}', name: 'payment_success')]
    public function success(int $lessonId, string $sessionId, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $lesson = $em->getRepository(Lesson::class)->find($lessonId);
        if (!$lesson) {
            throw $this->createNotFoundException('Leçon introuvable.');
        }

        // Vérifier si l’utilisateur a déjà acheté cette leçon
        $existing = $em->getRepository(Purchase::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson
        ]);

        if (!$existing) {
            $purchase = new Purchase();
            $purchase->setUser($user);
            $purchase->setLesson($lesson);
            $purchase->setType('lesson');

            $em->persist($purchase);
            $em->flush();
        }

        return $this->render('payment/success.html.twig', [
            'lesson' => $lesson,
            'sessionId' => $sessionId,
        ]);
    }

    #[Route('/cancel', name: 'payment_cancel')]
    public function cancel(): Response
    {
        return $this->render('payment/cancel.html.twig');
    }


    #[Route('/checkout/cursus/{id}', name: 'payment_checkout_cursus')]
    public function checkoutCursus(Cursus $cursus, Request $request): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $YOUR_DOMAIN = $request->getSchemeAndHttpHost();

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => intval($cursus->getPrice() * 100),
                    'product_data' => [
                        'name' => $cursus->getTitle(),
                    ],
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success-cursus/' . $cursus->getId() . '/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $YOUR_DOMAIN . '/cancel',
        ]);

        return $this->redirect($checkout_session->url);
    }

    #[Route('/success-cursus/{cursusId}/{sessionId}', name: 'payment_success_cursus')]
    public function successCursus(int $cursusId, string $sessionId, EntityManagerInterface $em): Response
    {
        $user = $this->getUser();
        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $cursus = $em->getRepository(Cursus::class)->find($cursusId);
        if (!$cursus) {
            throw $this->createNotFoundException('Cursus introuvable.');
        }

        // Vérifier si l’utilisateur a déjà acheté ce cursus
        $existingCursusPurchase = $em->getRepository(Purchase::class)->findOneBy([
            'user' => $user,
            'cursus' => $cursus,
            'type' => 'cursus'
        ]);

        if (!$existingCursusPurchase) {
            // Créer un purchase pour le cursus
            $purchase = new Purchase();
            $purchase->setUser($user);
            $purchase->setCursus($cursus);
            $purchase->setType('cursus');
            $em->persist($purchase);

            // Créer aussi un purchase pour chaque leçon du cursus
            foreach ($cursus->getLessons() as $lesson) {
                $existingLessonPurchase = $em->getRepository(Purchase::class)->findOneBy([
                    'user' => $user,
                    'lesson' => $lesson,
                    'type' => 'lesson'
                ]);

                if (!$existingLessonPurchase) { // ✅ évite les doublons
                    $lessonPurchase = new Purchase();
                    $lessonPurchase->setUser($user);
                    $lessonPurchase->setLesson($lesson);
                    $lessonPurchase->setType('lesson');
                    $em->persist($lessonPurchase);
                }
            }

            $em->flush();
        }

        return $this->render('payment/success_cursus.html.twig', [
            'cursus' => $cursus,
            'sessionId' => $sessionId,
        ]);
    }
}
