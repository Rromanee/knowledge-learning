<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Repository\LessonValidationRepository;
use App\Repository\OrderItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Displays lesson pages and purchased lessons.
 */
final class LessonController extends AbstractController
{
    /**
     * Displays a lesson page, accessible only if purchased.
     */
    #[Route('/lesson/{id}', name: 'app_lesson')]
    public function show(
        Lesson $lesson,
        OrderItemRepository $orderItemRepository,
        LessonValidationRepository $lessonValidationRepository
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $purchasedLesson = $orderItemRepository->findPurchasedLessonByUser(
            $lesson,
            $user
        );

        $purchasedCourse = $orderItemRepository->findPurchasedCourseByUser(
            $lesson->getCourse(),
            $user
        );

        if (!$purchasedLesson && !$purchasedCourse) {
            $this->addFlash(
                'warning',
                'Vous devez acheter cette leçon ou son cursus.'
            );

            return $this->redirectToRoute('app_course', [
                'id' => $lesson->getCourse()->getId(),
            ]);
        }

        $lessonValidation = $lessonValidationRepository
            ->findValidationByUserAndLesson(
                $lesson,
                $user
            );

        $isValidated = $lessonValidation !== null;

        return $this->render('lesson/index.html.twig', [
            'lesson' => $lesson,
            'isValidated' => $isValidated,
        ]);
    }

    /**
     * Displays a purchased lesson from the "Mes achats" section.
     */
    #[Route('/mes-achats/lesson/{id}', name: 'app_purchases_lesson')]
    public function purchases(
        Lesson $lesson,
        OrderItemRepository $orderItemRepository,
        LessonValidationRepository $lessonValidationRepository
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $purchasedLesson = $orderItemRepository->findPurchasedLessonByUser(
            $lesson,
            $user
        );

        $purchasedCourse = $orderItemRepository->findPurchasedCourseByUser(
            $lesson->getCourse(),
            $user
        );

        if (!$purchasedLesson && !$purchasedCourse) {
            throw $this->createAccessDeniedException();
        }

        $lessonValidation = $lessonValidationRepository
            ->findValidationByUserAndLesson(
                $lesson,
                $user
            );

        $isValidated = $lessonValidation !== null;

        return $this->render('order/purchases_lesson.html.twig', [
            'lesson' => $lesson,
            'isValidated' => $isValidated,
        ]);
    }
}