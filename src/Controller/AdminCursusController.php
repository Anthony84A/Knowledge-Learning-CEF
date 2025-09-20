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

/**
 * Class AdminCursusController
 *
 * This controller manages CRUD operations for cursus entities.
 * Accessible only to administrators (ROLE_ADMIN).
 *
 * @package App\Controller
 */
#[Route('/admin/cursus')]
#[IsGranted('ROLE_ADMIN')]
class AdminCursusController extends AbstractController
{
    /**
     * Displays the list of all cursus.
     *
     * @param EntityManagerInterface $em Entity manager to fetch cursus data.
     *
     * @return Response Returns the view containing the cursus list.
     */
    #[Route('/', name: 'admin_cursus_index')]
    public function index(EntityManagerInterface $em): Response
    {
        $cursus = $em->getRepository(Cursus::class)->findAll();

        return $this->render('admin/cursus/index.html.twig', [
            'cursus' => $cursus,
        ]);
    }

    /**
     * Creates a new cursus entity.
     * If the form is submitted and valid, the cursus is persisted and saved in the database.
     *
     * @param Request $request The current HTTP request (handles the form submission).
     * @param EntityManagerInterface $em Entity manager to persist the new cursus.
     *
     * @return Response Returns the form view if not submitted or invalid, otherwise redirects to cursus index.
     */
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

    /**
     * Edits an existing cursus entity.
     * If the form is submitted and valid, the changes are saved.
     *
     * @param Cursus $cursus The cursus entity to be updated.
     * @param Request $request The current HTTP request (handles the form submission).
     * @param EntityManagerInterface $em Entity manager to flush changes.
     *
     * @return Response Returns the form view if not submitted or invalid, otherwise redirects to cursus index.
     */
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

    /**
     * Deletes a cursus entity from the database.
     *
     * @param Cursus $cursus The cursus entity to be removed.
     * @param EntityManagerInterface $em Entity manager to handle the deletion.
     *
     * @return Response Redirects to the cursus index page after deletion.
     */
    #[Route('/{id}/delete', name: 'admin_cursus_delete')]
    public function delete(Cursus $cursus, EntityManagerInterface $em): Response
    {
        $em->remove($cursus);
        $em->flush();
        $this->addFlash('success', 'Cursus supprimé avec succès.');

        return $this->redirectToRoute('admin_cursus_index');
    }
}
