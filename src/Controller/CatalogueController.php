<?php

namespace App\Controller;

use App\Repository\CursusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CatalogueController extends AbstractController
{
    #[Route('/catalogue', name: 'catalogue')]
    public function index(CursusRepository $cursusRepository, EntityManagerInterface $em): Response
    {
        // Récupère tous les cursus avec leurs leçons et leurs achats
        $cursus = $em->createQueryBuilder()
            ->select('c', 'l', 'lp', 'cp')
            ->from('App\Entity\Cursus', 'c')
            ->leftJoin('c.lessons', 'l')
            ->leftJoin('c.purchases', 'cp')
            ->leftJoin('l.purchases', 'lp')
            ->getQuery()
            ->getResult();

        return $this->render('catalogue/index.html.twig', [
            'cursus' => $cursus,
        ]);
    }
}
