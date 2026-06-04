<?php

namespace App\Controller;

use App\Service\StripeService;
use App\Entity\Course;
use App\Entity\Lesson;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Repository\OrderItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/** Handles purchase redirections to Stripe for courses and lessons. */
final class OrderController extends AbstractController
{
    #[Route('/course/{id}/buy', name: 'app_course_buy')]
    public function buyCourse(
        Course $course,
        StripeService $stripeService,
        UrlGeneratorInterface $urlGenerator,
        OrderItemRepository $orderItemRepository
    ): Response {
        $courseAlreadyBought = $orderItemRepository->findPurchasedCourseByUser(
            $course,
            $this->getUser()
        );

        if ($courseAlreadyBought) {
            $this->addFlash(
                'warning',
                'Vous possédez déjà ce cursus.'
            );

            return $this->redirectToRoute('app_theme', [
                'id' => $course->getTheme()->getId(),
            ]);
        }

        $session = $stripeService->createCheckoutSession(
            $course->getTitle(),
            $course->getPrice(),
            $course->getId(),
            'course',
            'app_stripe_success',
            $urlGenerator
        );

        return $this->redirect($session->url);
    }

    #[Route('/lesson/{id}/buy', name: 'app_lesson_buy')]
    public function buyLesson(
        Lesson $lesson,
        StripeService $stripeService,
        UrlGeneratorInterface $urlGenerator,
        OrderItemRepository $orderItemRepository
    ): Response {
        $lessonAlreadyBought = $orderItemRepository->findPurchasedLessonByUser(
            $lesson,
            $this->getUser()
        );

        if ($lessonAlreadyBought) {
            $this->addFlash(
                'warning',
                'Vous possédez déjà cette leçon.'
            );

            return $this->redirectToRoute('app_course', [
                'id' => $lesson->getCourse()->getId(),
            ]);
        }

        $session = $stripeService->createCheckoutSession(
            $lesson->getTitle(),
            $lesson->getPrice(),
            $lesson->getId(),
            'lesson',
            'app_stripe_success',
            $urlGenerator
        );

        return $this->redirect($session->url);
    }
}