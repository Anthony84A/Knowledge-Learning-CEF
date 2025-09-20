<?php
namespace App\Controller;

use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class HomeController
 *
 * This controller handles the homepage of the application.
 * It displays the list of available themes.
 *
 * @package App\Controller
 */
class HomeController extends AbstractController
{
    /**
     * Displays the homepage with all available themes.
     *
     * @param ThemeRepository $themeRepository Repository to fetch theme data.
     *
     * @return Response Returns the homepage view with themes.
     */
    #[Route('/', name: 'home')]
    public function index(ThemeRepository $themeRepository): Response
    {
        $themes = $themeRepository->findAll();

        return $this->render('home/index.html.twig', [
            'themes' => $themes,
        ]);
    }
}
