<?php

namespace App\Controller\Admin;

use App\Entity\Theme;
use App\Form\ThemeType;
use App\Repository\ThemeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/** Manages theme CRUD operations in the backoffice. */
final class AdminThemeController extends AbstractController
{
    #[Route('/admin/themes', name: 'app_admin_themes')]
    public function index(
        ThemeRepository $themeRepository
    ): Response {
        return $this->render('admin/themes.html.twig', [
            'themes' => $themeRepository->findAll(),
        ]);
    }

    #[Route('/admin/themes/new', name: 'app_admin_theme_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $theme = new Theme();

        $form = $this->createForm(
            ThemeType::class,
            $theme
        );

        $form->handleRequest($request);

        if (
            $form->isSubmitted()
            && $form->isValid()
        ) {
            $entityManager->persist($theme);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Thème créé avec succès.'
            );

            return $this->redirectToRoute(
                'app_admin_themes'
            );
        }

        return $this->render('admin/theme_form.html.twig', [
            'form' => $form,
            'title' => 'Créer un thème',
        ]);
    }

    #[Route('/admin/themes/{id}/edit', name: 'app_admin_theme_edit')]
    public function edit(
        Theme $theme,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(
            ThemeType::class,
            $theme
        );

        $form->handleRequest($request);

        if (
            $form->isSubmitted()
            && $form->isValid()
        ) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Thème modifié avec succès.'
            );

            return $this->redirectToRoute(
                'app_admin_themes'
            );
        }

        return $this->render('admin/theme_form.html.twig', [
            'form' => $form,
            'title' => 'Modifier un thème',
        ]);
    }

    #[Route('/admin/themes/{id}/delete', name: 'app_admin_theme_delete')]
    public function delete(
        Theme $theme,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {

        if (!$this->isCsrfTokenValid('delete_theme_' . $theme->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }
        $entityManager->remove($theme);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Thème supprimé avec succès.'
        );

        return $this->redirectToRoute(
            'app_admin_themes'
        );
    }
}