<?php

namespace App\Controller;

use App\Entity\Cursus;
use App\Form\CursusType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/cursus')]
#[IsGranted('ROLE_ADMIN')]
class AdminCursusController extends AbstractController
{
    #[Route('/', name: 'admin_cursus_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $cursus = $em->getRepository(Cursus::class)->findAll();

        return $this->render('admin/cursus/index.html.twig', [
            'cursus' => $cursus,
        ]);
    }

    #[Route('/new', name: 'admin_cursus_new')]
    public function new(Request $request, EntityManagerInterface $em): Response
    {
        $cursus = new Cursus();
        $form = $this->createForm(CursusType::class, $cursus);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($cursus);
            $em->flush();
            $this->addFlash('success', 'Cursus créé avec succès !');

            return $this->redirectToRoute('admin_cursus_index');
        }

        return $this->render('admin/cursus/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'admin_cursus_edit')]
    public function edit(Cursus $cursus, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CursusType::class, $cursus);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Cursus modifié avec succès !');

            return $this->redirectToRoute('admin_cursus_index');
        }

        return $this->render('admin/cursus/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'admin_cursus_delete')]
    public function delete(Cursus $cursus, EntityManagerInterface $em): Response
    {
        $em->remove($cursus);
        $em->flush();
        $this->addFlash('success', 'Cursus supprimé avec succès.');

        return $this->redirectToRoute('admin_cursus_index');
    }
}
