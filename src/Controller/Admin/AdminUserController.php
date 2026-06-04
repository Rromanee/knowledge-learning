<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Form\UserType;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

/** Manages user accounts in the backoffice. */
final class AdminUserController extends AbstractController
{
    #[Route('/admin/users', name: 'app_admin_users')]
    public function users(
        UserRepository $userRepository
        ): Response {
            return $this->render('admin/users.html.twig', [
                'users' => $userRepository->findAll(),
            ]);
        }
        
    #[Route('/admin/users/{id}/edit', name: 'app_admin_user_edit')]
    public function editUser(
         User $user,            
         Request $request,            
         EntityManagerInterface $entityManager
        ): Response {
            $form = $this->createForm(UserType::class, $user);
            $form->handleRequest($request);
        
            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();
                $this->addFlash('success', 'Utilisateur modifié.');
                return $this->redirectToRoute('app_admin_users');
            }
        
            return $this->render('admin/user_form.html.twig', [
                'form' => $form,
                'user' => $user,
            ]);
        }
            
    #[Route('/admin/users/{id}/delete', name: 'app_admin_user_delete')]
    public function deleteUser(
        User $user,
        EntityManagerInterface $entityManager,
        Request $request
        ): Response {

            if (!$this->isCsrfTokenValid('delete_user_' . $user->getId(), $request->request->get('_token'))) {
                throw $this->createAccessDeniedException('Invalid CSRF token.');
            }

            $entityManager->remove($user);
            $entityManager->flush();
                
            $this->addFlash(
                'success',
                'Utilisateur supprimé.'
            );
                    
            return $this->redirectToRoute(
                'app_admin_users'
            );
        }

}