<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\CourseRepository;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class StripeController extends AbstractController
{
    #[Route('/stripe', name: 'app_stripe')]
    public function index(): Response
    {
        return $this->render('stripe/index.html.twig', [
            'controller_name' => 'StripeController',
        ]);
    }

    #[Route('/stripe/success', name: 'app_stripe_success')]
    public function success(
        Request $request,
        CourseRepository $courseRepository,
        LessonRepository $lessonRepository,
        EntityManagerInterface $entityManager
    ): Response {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $sessionId = $request->query->get('session_id');

        if (!$sessionId) {
            $this->addFlash(
                'warning',
                'Session Stripe introuvable.'
            );

            return $this->redirectToRoute('app_home');
        }

        $session = Session::retrieve($sessionId);

        $itemId = $session->metadata->item_id ?? null;
        $itemType = $session->metadata->item_type ?? null;

        if (!$itemId || !$itemType) {
            $this->addFlash(
                'warning',
                'Informations de paiement invalides.'
            );

            return $this->redirectToRoute('app_home');
        }

        $order = new Order();
        $order->setUser($this->getUser());
        $order->setStatus('paid');
        $order->setTotal($session->amount_total / 100);
        $order->setStripeSessionId($sessionId);

        $orderItem = new OrderItem();
        $orderItem->setCustomerOrder($order);

        if ($itemType === 'course') {
            $course = $courseRepository->find($itemId);

            if (!$course) {
                $this->addFlash(
                    'warning',
                    'Cursus introuvable.'
                );

                return $this->redirectToRoute('app_home');
            }

            $orderItem->setCourse($course);

            $entityManager->persist($order);
            $entityManager->persist($orderItem);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Paiement effectué avec succès.'
            );

            return $this->redirectToRoute('app_course', [
                'id' => $course->getId(),
            ]);
        }

        if ($itemType === 'lesson') {
            $lesson = $lessonRepository->find($itemId);

            if (!$lesson) {
                $this->addFlash(
                    'warning',
                    'Leçon introuvable.'
                );

                return $this->redirectToRoute('app_home');
            }

            $orderItem->setLesson($lesson);

            $entityManager->persist($order);
            $entityManager->persist($orderItem);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Paiement effectué avec succès.'
            );

            return $this->redirectToRoute('app_lesson', [
                'id' => $lesson->getId(),
            ]);
        }

        $this->addFlash(
            'warning',
            'Type d\'achat inconnu.'
        );

        return $this->redirectToRoute('app_home');
    }
}