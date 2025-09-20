<?php
namespace App\Controller;

use App\Entity\Cursus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class CursusController
 *
 * This controller handles the display of a single cursus and its related lessons.
 *
 * @package App\Controller
 */
class CursusController extends AbstractController
{
    /**
     * Displays the details of a specific cursus, including its lessons.
     *
     * @param Cursus $cursus The cursus entity automatically fetched by its ID.
     *
     * @return Response Returns the cursus detail view.
     */
    #[Route('/cursus/{id}', name: 'cursus_show', requirements: ['id' => '\d+'])]
    public function show(Cursus $cursus): Response
    {
        return $this->render('cursus/show.html.twig', [
            'cursus' => $cursus,
        ]);
    }
}
