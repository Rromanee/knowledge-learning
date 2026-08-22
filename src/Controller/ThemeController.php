<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Displays a theme page with its courses and purchase status.
 */
final class ThemeController extends AbstractController
{
    #[Route('/theme/{id}', name: 'app_theme')]
    public function show(Theme $theme): Response
    {
        /** @var User|null $user */
        $user = $this->getUser();

        $purchasedCourses = [];
        $coursesWithPurchasedLessons = [];

        if ($user) {
            foreach ($user->getOrders() as $order) {
                foreach ($order->getOrderItems() as $orderItem) {

                    // A course was purchased directly.
                    if ($orderItem->getCourse()) {
                        $purchasedCourses[] = $orderItem->getCourse()->getId();
                    }

                    // A lesson from a course was purchased.
                    elseif ($orderItem->getLesson()) {
                        $lesson = $orderItem->getLesson();
                        $course = $lesson->getCourse();

                        if ($course) {
                            $coursesWithPurchasedLessons[] = $course->getId();
                        }
                    }
                }
            }
        }

        // Remove duplicate course IDs.
        $purchasedCourses = array_unique($purchasedCourses);
        $coursesWithPurchasedLessons = array_unique($coursesWithPurchasedLessons);

        // Check whether all lessons of a course have been purchased.
        foreach ($theme->getCourses() as $course) {

            // Skip courses already purchased directly.
            if (in_array($course->getId(), $purchasedCourses, true)) {
                continue;
            }

            $lessons = $course->getLessons();
            $totalLessons = count($lessons);

            if ($totalLessons === 0) {
                continue;
            }

            $purchasedLessonCount = 0;

            foreach ($lessons as $lesson) {
                foreach ($user?->getOrders() ?? [] as $order) {
                    foreach ($order->getOrderItems() as $orderItem) {

                        if (
                            $orderItem->getLesson()
                            && $orderItem->getLesson()->getId() === $lesson->getId()
                        ) {
                            $purchasedLessonCount++;
                            break 2;
                        }
                    }
                }
            }

            // All lessons have been purchased individually.
            if ($purchasedLessonCount === $totalLessons) {
                $purchasedCourses[] = $course->getId();
            }
        }

        // Remove duplicates after checking all lessons.
        $purchasedCourses = array_unique($purchasedCourses);

        return $this->render('theme/index.html.twig', [
            'theme' => $theme,
            'purchasedCourses' => $purchasedCourses,
            'coursesWithPurchasedLessons' => $coursesWithPurchasedLessons,
        ]);
    }
}
