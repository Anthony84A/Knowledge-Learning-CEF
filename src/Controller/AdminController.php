<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\CursusRepository;
use App\Repository\LessonRepository;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/', name: 'admin_dashboard')]
    public function index(
        UserRepository $userRepo,
        CursusRepository $cursusRepo,
        LessonRepository $lessonRepo,
    ): Response {
        $nbUsers = $userRepo->count([]);
        $nbCursus = $cursusRepo->count([]);
        $nbLessons = $lessonRepo->count([]);

        return $this->render('admin/dashboard.html.twig', [
            'nbUsers' => $nbUsers,
            'nbCursus' => $nbCursus,
            'nbLessons' => $nbLessons,
        ]);
    }

    #[Route('/users', name: 'admin_users')]
    public function users(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/user/{id}/toggle-admin', name: 'admin_toggle_role')]
    public function toggleAdmin(User $user, EntityManagerInterface $em): Response
    {
        $roles = $user->getRoles();

        if (in_array('ROLE_ADMIN', $roles)) {
            $roles = array_diff($roles, ['ROLE_ADMIN']);
            $this->addFlash('success', "Le rôle administrateur a été retiré à {$user->getEmail()}");
        } else {
            $roles[] = 'ROLE_ADMIN';
            $this->addFlash('success', "Le rôle administrateur a été ajouté à {$user->getEmail()}");
        }

        $user->setRoles($roles);
        $em->flush();

        return $this->redirectToRoute('admin_users');
    }

    #[Route('/user/{id}/delete', name: 'admin_delete_user')]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', "Utilisateur supprimé avec succès");

        return $this->redirectToRoute('admin_users');
    }
}
