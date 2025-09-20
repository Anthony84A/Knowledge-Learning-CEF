<?php

namespace App\Controller;

use App\Entity\Theme;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Controller responsible for displaying themes.
 *
 * Handles:
 * - Displaying a single theme and its related courses (cursuses)
 */
class ThemeController extends AbstractController
{
    /**
     * Displays a single theme and its associated cursuses.
     *
     * @param Theme $theme The theme entity to display (auto-resolved by Symfony via ParamConverter)
     *
     * @return Response Renders the theme details page
     */
    #[Route('/theme/{id}', name: 'theme_show', requirements: ['id' => '\d+'])]
    public function show(Theme $theme): Response
    {
        // The $theme entity includes its associated cursuses via getCursuses()
        return $this->render('theme/show.html.twig', [
            'theme' => $theme,
        ]);
    }
}
