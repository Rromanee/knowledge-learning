<?php

namespace App\Controller;

use App\Entity\Course;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CourseController extends AbstractController
{
    #[Route('/course/{id}', name: 'app_course')]
    public function show(Course $course): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        
        $purchasedLessons = [];
        
        if ($user) {
            foreach ($user->getOrders() as $order) {
                foreach ($order->getOrderItems() as $orderItem) {
                    if ($orderItem->getLesson()) {
                        $purchasedLessons[] = $orderItem->getLesson()->getId();
                    }
                }
            }
        }

        return $this->render('course/index.html.twig', [
            'course' => $course,
            'purchasedLessons' => $purchasedLessons,
        ]);
    }
}