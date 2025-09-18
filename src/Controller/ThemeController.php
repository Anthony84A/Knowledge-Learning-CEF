<?php
namespace App\Controller;

use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ThemeController extends AbstractController
{
    #[Route('/theme/{id}', name: 'theme_show', requirements: ['id' => '\d+'])]
    public function show(Theme $theme): Response
    {
        // $theme contient ses cursus via getCursuses()
        return $this->render('theme/show.html.twig', [
            'theme' => $theme,
        ]);
    }
}
