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

#[Route('/admin/lesson')]
#[IsGranted('ROLE_ADMIN')]
class AdminLessonController extends AbstractController
{
    #[Route('/', name: 'admin_lesson_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $lessons = $em->getRepository(Lesson::class)->findAll();

        return $this->render('admin/lesson/index.html.twig', [
            'lessons' => $lessons,
        ]);
    }

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

    #[Route('/{id}/delete', name: 'admin_lesson_delete')]
    public function delete(Lesson $lesson, EntityManagerInterface $em): Response
    {
        $em->remove($lesson);
        $em->flush();
        $this->addFlash('success', 'Leçon supprimée avec succès.');

        return $this->redirectToRoute('admin_lesson_index');
    }
}
