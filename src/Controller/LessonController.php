<?php
namespace App\Controller;

use App\Entity\Lesson;
use App\Entity\LessonValidation;
use App\Entity\Certification;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class LessonController
 *
 * This controller handles the display of lessons and their validation by users.
 * It also manages the automatic certification when all lessons of a cursus are validated.
 *
 * @package App\Controller
 */
class LessonController extends AbstractController
{
    /**
     * Displays a single lesson page.
     *
     * @param Lesson $lesson The lesson entity to be displayed.
     *
     * @return Response Returns the lesson detail view.
     */
    #[Route('/lesson/{id}', name: 'lesson_show', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function show(Lesson $lesson): Response
    {
        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    /**
     * Validates a lesson for the currently logged-in user.
     *
     * Steps performed:
     *  - Checks if the user has purchased the lesson or its cursus.
     *  - Prevents duplicate validations.
     *  - Creates or updates a lesson validation entry.
     *  - Checks if all lessons of the cursus are validated, and grants a certification if so.
     *
     * @param Lesson $lesson The lesson entity to be validated.
     * @param PurchaseRepository $purchaseRepository Repository used to check user purchases.
     * @param EntityManagerInterface $em Entity manager to persist and update validations/certifications.
     *
     * @return Response Redirects back to the lesson page with flash messages.
     */
    #[Route('/lesson/{id}/validate', name: 'lesson_validate')]
    #[IsGranted('ROLE_USER')]
    public function validateLesson(
        Lesson $lesson,
        PurchaseRepository $purchaseRepository,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        // Check if user purchased this lesson or the full cursus
        $purchaseLesson = $purchaseRepository->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
            'type' => 'lesson'
        ]);

        $purchaseCursus = $purchaseRepository->findOneBy([
            'user' => $user,
            'cursus' => $lesson->getCursus(),
            'type' => 'cursus'
        ]);

        if (!$purchaseLesson && !$purchaseCursus) {
            $this->addFlash('error', 'Vous devez acheter cette leçon ou le cursus complet avant de la valider.');
            return $this->redirectToRoute('lesson_show', ['id' => $lesson->getId()]);
        }

        // Check if lesson validation already exists
        $existingValidation = $em->getRepository(LessonValidation::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
        ]);

        if ($existingValidation && $existingValidation->isValidated()) {
            $this->addFlash('info', 'Vous avez déjà validé cette leçon.');
            return $this->redirectToRoute('lesson_show', ['id' => $lesson->getId()]);
        }

        // Create or update validation
        $validation = $existingValidation ?? new LessonValidation();
        $validation->setUser($user);
        $validation->setLesson($lesson);
        $validation->setIsValidated(true);

        $em->persist($validation);
        $em->flush();

        // Check if all lessons of the cursus are validated
        $cursus = $lesson->getCursus();
        $allLessons = $cursus->getLessons();

        $validatedLessons = $em->getRepository(LessonValidation::class)->count([
            'user' => $user,
            'isValidated' => true,
            'lesson' => $allLessons->toArray(),
        ]);

        if ($validatedLessons === count($allLessons)) {
            // Grant certification if not already obtained
            $existingCert = $em->getRepository(Certification::class)->findOneBy([
                'user' => $user,
                'cursus' => $cursus,
            ]);

            if (!$existingCert) {
                $certification = new Certification();
                $certification->setUser($user);
                $certification->setCursus($cursus);
                $certification->setObtainedAt(new \DateTimeImmutable());

                $em->persist($certification);
                $em->flush();

                $this->addFlash('success', '🎉 Félicitations ! Vous avez obtenu la certification du cursus "' . $cursus->getTitle() . '"');
            }
        }

        $this->addFlash('success', 'Leçon validée avec succès ✅');
        return $this->redirectToRoute('lesson_show', ['id' => $lesson->getId()]);
    }
}
