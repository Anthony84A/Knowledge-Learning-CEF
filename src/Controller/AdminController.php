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

/**
 * Class AdminController
 *
 * This controller handles all the administrative features of the application.
 * It is accessible only to users with the ROLE_ADMIN role.
 *
 * @package App\Controller
 */
#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    /**
     * Displays the admin dashboard with some key statistics:
     * - number of users
     * - number of cursus
     * - number of lessons
     *
     * @param UserRepository $userRepo Repository to fetch user data.
     * @param CursusRepository $cursusRepo Repository to fetch cursus data.
     * @param LessonRepository $lessonRepo Repository to fetch lesson data.
     *
     * @return Response Returns the dashboard view with statistics.
     */
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

    /**
     * Displays the list of all users.
     *
     * @param EntityManagerInterface $em Entity manager used to fetch users.
     *
     * @return Response Returns a view with the list of users.
     */
    #[Route('/users', name: 'admin_users')]
    public function users(EntityManagerInterface $em): Response
    {
        $users = $em->getRepository(User::class)->findAll();

        return $this->render('admin/users.html.twig', [
            'users' => $users,
        ]);
    }

    /**
     * Toggles the admin role for a given user.
     * - If the user already has ROLE_ADMIN, it is removed.
     * - Otherwise, the role is added.
     *
     * A success flash message is displayed after the change.
     *
     * @param User $user The user whose role will be toggled.
     * @param EntityManagerInterface $em Entity manager used to persist changes.
     *
     * @return Response Redirects to the admin users page.
     */
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

    /**
     * Deletes a user from the database.
     * A success flash message is displayed after deletion.
     *
     * @param User $user The user entity to be deleted.
     * @param EntityManagerInterface $em Entity manager used to remove the user.
     *
     * @return Response Redirects to the admin users page.
     */
    #[Route('/user/{id}/delete', name: 'admin_delete_user')]
    public function deleteUser(User $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();

        $this->addFlash('success', "Utilisateur supprimé avec succès");

        return $this->redirectToRoute('admin_users');
    }
}
