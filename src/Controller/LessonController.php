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

class LessonController extends AbstractController
{
    #[Route('/lesson/{id}', name: 'lesson_show', requirements: ['id' => '\d+'])]
    #[IsGranted('ROLE_USER')]
    public function show(Lesson $lesson): Response
    {
        return $this->render('lesson/show.html.twig', [
            'lesson' => $lesson,
        ]);
    }

    #[Route('/lesson/{id}/validate', name: 'lesson_validate')]
    #[IsGranted('ROLE_USER')]
    public function validateLesson(
        Lesson $lesson,
        PurchaseRepository $purchaseRepository,
        EntityManagerInterface $em
    ): Response {
        $user = $this->getUser();

        // 1. Vérifier que l’utilisateur a bien acheté la leçon ou le cursus complet
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

        // 2. Vérifier si une validation existe déjà
        $existingValidation = $em->getRepository(LessonValidation::class)->findOneBy([
            'user' => $user,
            'lesson' => $lesson,
        ]);

        if ($existingValidation && $existingValidation->isValidated()) {
            $this->addFlash('info', 'Vous avez déjà validé cette leçon.');
            return $this->redirectToRoute('lesson_show', ['id' => $lesson->getId()]);
        }

        // 3. Créer ou mettre à jour la validation
        $validation = $existingValidation ?? new LessonValidation();
        $validation->setUser($user);
        $validation->setLesson($lesson);
        $validation->setIsValidated(true);

        $em->persist($validation);
        $em->flush();

        // 4. Vérifier si toutes les leçons du cursus sont validées
        $cursus = $lesson->getCursus();
        $allLessons = $cursus->getLessons();

        $validatedLessons = $em->getRepository(LessonValidation::class)->count([
            'user' => $user,
            'isValidated' => true,
            'lesson' => $allLessons->toArray(),
        ]);

        if ($validatedLessons === count($allLessons)) {
            // Vérifier si certification déjà obtenue
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
