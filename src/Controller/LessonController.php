<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Repository\OrderItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class LessonController extends AbstractController
{
    #[Route('/lesson/{id}', name: 'app_lesson')]
    public function show(
        Lesson $lesson,
        OrderItemRepository $orderItemRepository
        ): Response
    {
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
                
        return $this->render('lesson/index.html.twig', [
            'lesson' => $lesson,
        ]);
    }
}
