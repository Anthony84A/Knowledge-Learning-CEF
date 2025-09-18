<?php
namespace App\Controller;

use App\Entity\Cursus;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CursusController extends AbstractController
{
    #[Route('/cursus/{id}', name: 'cursus_show', requirements: ['id' => '\d+'])]
    public function show(Cursus $cursus): Response
    {
        // $cursus contient ses lessons via getLessons()
        return $this->render('cursus/show.html.twig', [
            'cursus' => $cursus,
        ]);
    }
}
