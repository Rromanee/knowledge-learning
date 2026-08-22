<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Theme;
use App\Repository\LessonValidationRepository;
use App\Repository\OrderItemRepository;
use App\Repository\OrderRepository;
use App\Service\StripeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Handles purchase redirections to Stripe and displays the purchase history.
 */
final class OrderController extends AbstractController
{
    /**
     * Redirects the user to Stripe to purchase a course.
     */
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

    /**
     * Redirects the user to Stripe to purchase a lesson.
     */
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

    /**
     * Displays the purchase history of the currently logged-in user.
     *
     * Calculates the lesson progression for each purchased course.
     */
    #[Route('/mes-achats', name: 'app_purchases')]
    public function purchases(
        OrderRepository $orderRepository,
        LessonValidationRepository $lessonValidationRepository
    ): Response {
        $user = $this->getUser();
    
        if (!$user) {
            throw $this->createAccessDeniedException();
        }
    
        $orders = $orderRepository->findByUser($user);
    
        $themes = [];
        $courses = [];
    
        foreach ($orders as $order) {
            foreach ($order->getOrderItems() as $orderItem) {
    
                // Course purchased directly.
                if ($orderItem->getCourse()) {
                    $course = $orderItem->getCourse();
    
                    if (!in_array($course, $courses, true)) {
                        $courses[] = $course;
                    }
    
                    $theme = $course->getTheme();
    
                    if ($theme && !in_array($theme, $themes, true)) {
                        $themes[] = $theme;
                    }
                }
    
                // Lesson purchased individually.
                elseif ($orderItem->getLesson()) {
                    $lesson = $orderItem->getLesson();
                    $course = $lesson->getCourse();
    
                    if ($course && !in_array($course, $courses, true)) {
                        $courses[] = $course;
                    }
    
                    $theme = $course?->getTheme();
    
                    if ($theme && !in_array($theme, $themes, true)) {
                        $themes[] = $theme;
                    }
                }
            }
        }
    
        /*
         * Calculate the progression of each purchased course.
         */
        $progressions = [];
    
        foreach ($courses as $course) {
    
            $totalLessons = 0;
            $validatedLessons = 0;
    
            /*
             * Determine which lessons are accessible:
             * - all lessons if the whole course was purchased;
             * - only individually purchased lessons otherwise.
             */
            $coursePurchased = false;
            $purchasedLessons = [];
    
            foreach ($orders as $order) {
                foreach ($order->getOrderItems() as $orderItem) {
    
                    if (
                        $orderItem->getCourse()
                        && $orderItem->getCourse()->getId() === $course->getId()
                    ) {
                        $coursePurchased = true;
                    }
    
                    if (
                        $orderItem->getLesson()
                        && $orderItem->getLesson()->getCourse()
                        && $orderItem->getLesson()->getCourse()->getId() === $course->getId()
                    ) {
                        $purchasedLessons[] = $orderItem->getLesson();
                    }
                }
            }
    
            /*
             * If the whole course was purchased,
             * all its lessons are part of the progression.
             */
            if ($coursePurchased) {
                $lessons = $course->getLessons()->toArray();
            } else {
                $lessons = [];
    
                foreach ($purchasedLessons as $lesson) {
                    if (!in_array($lesson, $lessons, true)) {
                        $lessons[] = $lesson;
                    }
                }
            }
    
            $totalLessons = count($lessons);
    
            /*
             * Count validated lessons.
             */
            foreach ($lessons as $lesson) {
    
                $validation = $lessonValidationRepository
                    ->findValidationByUserAndLesson(
                        $lesson,
                        $user
                    );
    
                if ($validation) {
                    $validatedLessons++;
                }
            }
    
            $percentage = 0;
    
            if ($totalLessons > 0) {
                $percentage = (int) round(
                    ($validatedLessons / $totalLessons) * 100
                );
            }
    
            $progressions[] = [
                'course' => $course,
                'validatedLessons' => $validatedLessons,
                'totalLessons' => $totalLessons,
                'percentage' => $percentage,
            ];
        }
    
        return $this->render('order/purchases.html.twig', [
            'themes' => $themes,
            'progressions' => $progressions,
        ]);
    }

    /**
     * Displays the purchased content of a specific course.
     */
    #[Route('/mes-achats/course/{id}', name: 'app_purchases_course')]
    public function purchasesCourse(
        Course $course,
        OrderItemRepository $orderItemRepository
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createAccessDeniedException();
        }

        $purchasedItems = $orderItemRepository->findPurchasedItemsByUserAndTheme(
            $user,
            $course->getTheme()
        );

        $purchasedLessons = [];
        $coursePurchased = false;

        foreach ($purchasedItems as $item) {
            // The whole course was purchased.
            if (
                $item->getCourse()
                && $item->getCourse()->getId() === $course->getId()
            ) {
                $coursePurchased = true;
            }

            // A lesson belonging to this course was purchased.
            if (
                $item->getLesson()
                && $item->getLesson()->getCourse()
                && $item->getLesson()->getCourse()->getId() === $course->getId()
            ) {
                $lesson = $item->getLesson();

                if (!in_array($lesson, $purchasedLessons, true)) {
                    $purchasedLessons[] = $lesson;
                }
            }
        }

        // If the whole course was purchased, all lessons are accessible.
        if ($coursePurchased) {
            $purchasedLessons = $course->getLessons()->toArray();
        }

        return $this->render('order/purchases_course.html.twig', [
            'course' => $course,
            'purchasedLessons' => $purchasedLessons,
            'coursePurchased' => $coursePurchased,
        ]);
    }
}