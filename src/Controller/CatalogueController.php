<?php

namespace App\Controller;

use App\Repository\CursusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class CatalogueController
 *
 * This controller handles the display of the training catalogue.
 * It retrieves cursus with their lessons and purchases to be displayed in the catalogue view.
 *
 * @package App\Controller
 */
class CatalogueController extends AbstractController
{
    /**
     * Displays the full training catalogue with cursus, lessons, and related purchases.
     *
     * @param CursusRepository $cursusRepository Repository to interact with cursus data (not used directly here).
     * @param EntityManagerInterface $em Entity manager used to build the custom query.
     *
     * @return Response Returns the catalogue view with cursus and their related data.
     */
    #[Route('/catalogue', name: 'catalogue')]
    public function index(CursusRepository $cursusRepository, EntityManagerInterface $em): Response
    {
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
