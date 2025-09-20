<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Form\LessonType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Class AdminLessonController
 *
 * This controller manages CRUD operations for lesson entities.
 * Accessible only to administrators (ROLE_ADMIN).
 *
 * @package App\Controller
 */
#[Route('/admin/lesson')]
#[IsGranted('ROLE_ADMIN')]
class AdminLessonController extends AbstractController
{
    /**
     * Displays the list of all lessons.
     *
     * @param EntityManagerInterface $em Entity manager to fetch lesson data.
     *
     * @return Response Returns the view containing the lesson list.
     */
    #[Route('/', name: 'admin_lesson_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $lessons = $em->getRepository(Lesson::class)->findAll();

        return $this->render('admin/lesson/index.html.twig', [
            'lessons' => $lessons,
        ]);
    }

    /**
     * Creates a new lesson entity.
     * If the form is submitted and valid, the lesson is persisted and saved in the database.
     *
     * @param Request $request The current HTTP request (handles the form submission).
     * @param EntityManagerInterface $em Entity manager to persist the new lesson.
     *
     * @return Response Returns the form view if not submitted or invalid, otherwise redirects to lesson index.
     */
    #[Route('/new', name: 'admin_lesson_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($lesson);
            $em->flush();
            $this->addFlash('success', 'Leçon créée avec succès !');

            return $this->redirectToRoute('admin_lesson_index');
        }

        return $this->render('admin/lesson/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Edits an existing lesson entity.
     * If the form is submitted and valid, the changes are saved.
     *
     * @param Lesson $lesson The lesson entity to be updated.
     * @param Request $request The current HTTP request (handles the form submission).
     * @param EntityManagerInterface $em Entity manager to flush changes.
     *
     * @return Response Returns the form view if not submitted or invalid, otherwise redirects to lesson index.
     */
    #[Route('/{id}/edit', name: 'admin_lesson_edit')]
    public function edit(Lesson $lesson, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(LessonType::class, $lesson);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Leçon modifiée avec succès !');

            return $this->redirectToRoute('admin_lesson_index');
        }

        return $this->render('admin/lesson/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * Deletes a lesson entity from the database.
     *
     * @param Lesson $lesson The lesson entity to be removed.
     * @param EntityManagerInterface $em Entity manager to handle the deletion.
     *
     * @return Response Redirects to the lesson index page after deletion.
     */
    #[Route('/{id}/delete', name: 'admin_lesson_delete')]
    public function delete(Lesson $lesson, EntityManagerInterface $em): Response
    {
        $em->remove($lesson);
        $em->flush();
        $this->addFlash('success', 'Leçon supprimée avec succès.');

        return $this->redirectToRoute('admin_lesson_index');
    }
}
