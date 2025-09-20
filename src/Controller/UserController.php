<?php

namespace App\Controller;

use App\Repository\PurchaseRepository;
use App\Entity\Certification;
use Doctrine\ORM\EntityManagerInterface;
use Dompdf\Dompdf;
use Dompdf\Options;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Controller responsible for user-specific actions.
 *
 * Handles:
 * - Displaying user courses
 * - Displaying user certifications
 * - Downloading certification PDFs
 */
class UserController extends AbstractController
{
    /**
     * Displays all courses purchased by the logged-in user.
     *
     * @param PurchaseRepository $purchaseRepository Repository to fetch purchases
     *
     * @return Response Renders the user's courses page
     */
    #[Route('/mes-cours', name: 'user_courses')]
    public function courses(PurchaseRepository $purchaseRepository): Response
    {
        $user = $this->getUser();

        if (!$user) {
            return $this->redirectToRoute('app_login');
        }

        $purchases = $purchaseRepository->findBy(['user' => $user]);

        return $this->render('user/courses.html.twig', [
            'purchases' => $purchases,
        ]);
    }

    /**
     * Displays all certifications obtained by the logged-in user.
     *
     * @param EntityManagerInterface $em Service to access database entities
     *
     * @return Response Renders the user's certifications page
     */
    #[Route('/mes-certifications', name: 'user_certifications')]
    #[IsGranted('ROLE_USER')]
    public function certifications(EntityManagerInterface $em): Response
    {
        $user = $this->getUser();

        $certifications = $em->getRepository(Certification::class)->findBy([
            'user' => $user
        ]);

        return $this->render('user/certifications.html.twig', [
            'certifications' => $certifications,
        ]);
    }

    /**
     * Generates and downloads a PDF for a specific certification.
     *
     * @param Certification $certification The certification entity to generate PDF for
     *
     * @return Response PDF file download
     */
    #[Route('/certification/{id}/download', name: 'user_certification_download')]
    #[IsGranted('ROLE_USER')]
    public function downloadCertification(Certification $certification): Response
    {
        $user = $this->getUser();

        if ($certification->getUser() !== $user) {
            throw $this->createAccessDeniedException("You do not have access to this certification.");
        }

        // Setup PDF options
        $options = new Options();
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new Dompdf($options);

        // Render HTML template to PDF
        $html = $this->renderView('certification/pdf.html.twig', [
            'certification' => $certification,
            'user' => $user,
        ]);

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'landscape');
        $dompdf->render();

        $fileName = 'certification_' . $certification->getId() . '.pdf';

        return new Response(
            $dompdf->stream($fileName, ["Attachment" => true]),
            Response::HTTP_OK,
            ['Content-Type' => 'application/pdf']
        );
    }
}
