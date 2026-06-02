<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class OrderController extends AbstractController
{
    #[Route('/course/{id}/buy', name: 'app_course_buy')]
    public function buyCourse(Course $course, 
    EntityManagerInterface $entityManager,
    OrderItemRepository $orderItemRepository): Response
    {
        $order = new Order();
        $order->setUser($this->getUser());
        $order->setStatus('paid');
        $order->setTotal($course->getPrice());

        $orderItem = new OrderItem();
        $orderItem->setCustomerOrder($order);
        $orderItem->setCourse($course);

        $courseAlreadyBought = $orderItemRepository->findPurchasedCourseByUser(
            $course,
            $this->getUser()
        );

        if ($courseAlreadyBought) {
            $this->addFlash('warning', 'Vous possédez déjà ce cursus.');
        
            return $this->redirectToRoute('app_theme', [
                'id' => $course->getTheme()->getId(),
            ]);
        }

        $entityManager->persist($order);
        $entityManager->persist($orderItem);
        $entityManager->flush();     
        
        return $this->redirectToRoute('app_course', [
            'id' => $course->getId(),
        ]);
    }

    #[Route('/lesson/{id}/buy', name: 'app_lesson_buy')]
    public function buyLesson(
        Lesson $lesson,
        EntityManagerInterface $entityManager,
        OrderItemRepository $orderItemRepository
        ): Response  
        {
            $order = new Order();
        $order->setUser($this->getUser());
        $order->setStatus('paid');
        $order->setTotal($lesson->getPrice());

        $orderItem = new OrderItem();
        $orderItem->setCustomerOrder($order);
        $orderItem->setLesson($lesson);

        $lessonAlreadyBought = $orderItemRepository->findPurchasedLessonByUser(
            $lesson,
            $this->getUser()
        );

        if ($lessonAlreadyBought) {
            $this->addFlash('warning', 'Vous possédez déjà cette leçon.');
        
            return $this->redirectToRoute('app_course', [
                'id' => $lesson->getCourse()->getId(),
            ]);
        }

        $entityManager->persist($order);
        $entityManager->persist($orderItem);
        $entityManager->flush();     
        
        return $this->redirectToRoute('app_lesson', [
            'id' => $lesson->getId(),
        ]);
    }
}
