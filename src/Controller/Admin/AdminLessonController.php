<?php

namespace App\Controller\Admin;

use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/** Manages lesson CRUD operations in the backoffice. */
final class AdminLessonController extends AbstractController
{
    #[Route('/admin/lessons', name: 'app_admin_lessons')]
    public function index(
        LessonRepository $lessonRepository
    ): Response {
        return $this->render('admin/lessons.html.twig', [
            'lessons' => $lessonRepository->findAll(),
        ]);
    }

    #[Route('/admin/lessons/new', name: 'app_admin_lesson_new')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $lesson = new Lesson();

        $form = $this->createForm(
            LessonType::class,
            $lesson
        );

        $form->handleRequest($request);

        if (
            $form->isSubmitted()
            && $form->isValid()
        ) {
            $entityManager->persist($lesson);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Leçon créée avec succès.'
            );

            return $this->redirectToRoute(
                'app_admin_lessons'
            );
        }

        return $this->render('admin/lesson_form.html.twig', [
            'form' => $form,
            'title' => 'Créer une leçon',
        ]);
    }

    #[Route('/admin/lessons/{id}/edit', name: 'app_admin_lesson_edit')]
    public function edit(
        Lesson $lesson,
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(
            LessonType::class,
            $lesson
        );

        $form->handleRequest($request);

        if (
            $form->isSubmitted()
            && $form->isValid()
        ) {
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Leçon modifiée avec succès.'
            );

            return $this->redirectToRoute(
                'app_admin_lessons'
            );
        }

        return $this->render('admin/lesson_form.html.twig', [
            'form' => $form,
            'title' => 'Modifier une leçon',
        ]);
    }

    #[Route('/admin/lessons/{id}/delete', name: 'app_admin_lesson_delete')]
    public function delete(
        Lesson $lesson,
        EntityManagerInterface $entityManager,
        Request $request
    ): Response {

        if (!$this->isCsrfTokenValid('delete_lesson_' . $lesson->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Invalid CSRF token.');
        }
        $entityManager->remove($lesson);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Leçon supprimée avec succès.'
        );

        return $this->redirectToRoute(
            'app_admin_lessons'
        );
    }
}