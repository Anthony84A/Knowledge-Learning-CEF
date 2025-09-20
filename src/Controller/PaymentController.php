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
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class PaymentController
 *
 * This controller manages the payment workflow using Stripe Checkout.
 * It supports both lesson purchases and cursus purchases, handling
 * the creation of purchase records upon successful payment.
 *
 * @package App\Controller
 */
class PaymentController extends AbstractController
{
    /**
     * Initiates a Stripe Checkout session for purchasing a single lesson.
     *
     * @param Lesson $lesson The lesson entity being purchased.
     * @param Request $request The current HTTP request, used to build success/cancel URLs.
     *
     * @return Response Redirects the user to the Stripe Checkout page.
     */
    #[Route('/checkout/{id}', name: 'payment_checkout')]
    #[IsGranted('ROLE_USER')]
    public function checkout(Lesson $lesson, Request $request): Response
    {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
        $YOUR_DOMAIN = $request->getSchemeAndHttpHost();

        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => intval($lesson->getPrice() * 100),
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

    /**
     * Handles a successful lesson purchase after returning from Stripe Checkout.
     * If the purchase does not already exist, it is created and persisted.
     *
     * @param int $lessonId The ID of the purchased lesson.
     * @param string $sessionId The Stripe Checkout session ID.
     * @param EntityManagerInterface $em Entity manager to handle database operations.
     *
     * @return Response Renders the success page with lesson details.
     */
    #[Route('/success/{lessonId}/{sessionId}', name: 'payment_success')]
    #[IsGranted('ROLE_USER')]
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

    /**
     * Displays the cancellation page when a Stripe Checkout session is cancelled.
     *
     * @return Response Returns the cancel page view.
     */
    #[Route('/cancel', name: 'payment_cancel')]
    #[IsGranted('ROLE_USER')]
    public function cancel(): Response
    {
        return $this->render('payment/cancel.html.twig');
    }

    /**
     * Initiates a Stripe Checkout session for purchasing a full cursus.
     *
     * @param Cursus $cursus The cursus entity being purchased.
     * @param Request $request The current HTTP request, used to build success/cancel URLs.
     *
     * @return Response Redirects the user to the Stripe Checkout page.
     */
    #[Route('/checkout/cursus/{id}', name: 'payment_checkout_cursus')]
    #[IsGranted('ROLE_USER')]
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

    /**
     * Handles a successful cursus purchase after returning from Stripe Checkout.
     * Creates a purchase for the cursus and, if not already present, for each lesson it contains.
     *
     * @param int $cursusId The ID of the purchased cursus.
     * @param string $sessionId The Stripe Checkout session ID.
     * @param EntityManagerInterface $em Entity manager to handle database operations.
     *
     * @return Response Renders the success page with cursus details.
     */
    #[Route('/success-cursus/{cursusId}/{sessionId}', name: 'payment_success_cursus')]
    #[IsGranted('ROLE_USER')]
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

        $existingCursusPurchase = $em->getRepository(Purchase::class)->findOneBy([
            'user' => $user,
            'cursus' => $cursus,
            'type' => 'cursus'
        ]);

        if (!$existingCursusPurchase) {
            $purchase = new Purchase();
            $purchase->setUser($user);
            $purchase->setCursus($cursus);
            $purchase->setType('cursus');
            $em->persist($purchase);

            foreach ($cursus->getLessons() as $lesson) {
                $existingLessonPurchase = $em->getRepository(Purchase::class)->findOneBy([
                    'user' => $user,
                    'lesson' => $lesson,
                    'type' => 'lesson'
                ]);

                if (!$existingLessonPurchase) {
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
