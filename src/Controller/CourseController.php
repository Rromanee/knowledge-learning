<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\User;
use App\Repository\OrderItemRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Displays a course page with its lessons and purchase status.
 */
final class CourseController extends AbstractController
{
    #[Route('/course/{id}', name: 'app_course')]
    public function show(
        Course $course,
        OrderItemRepository $orderItemRepository
    ): Response {
        /** @var User|null $user */
        $user = $this->getUser();

        $coursePurchased = false;
        $purchasedLessons = [];

        if ($user) {

            // Check if the whole course has been purchased.
            $purchasedCourse = $orderItemRepository->findPurchasedCourseByUser(
                $course,
                $user
            );

            $coursePurchased = !empty($purchasedCourse);

            // Check which lessons were purchased individually.
            foreach ($course->getLessons() as $lesson) {

                $purchasedLesson = $orderItemRepository->findPurchasedLessonByUser(
                    $lesson,
                    $user
                );

                if (!empty($purchasedLesson)) {
                    $purchasedLessons[] = $lesson->getId();
                }
            }
        }

        return $this->render('course/index.html.twig', [
            'course' => $course,
            'coursePurchased' => $coursePurchased,
            'purchasedLessons' => $purchasedLessons,
        ]);
    }
}
