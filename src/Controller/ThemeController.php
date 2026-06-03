<?php

namespace App\Controller;

use App\Entity\Theme;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ThemeController extends AbstractController
{
    #[Route('/theme/{id}', name: 'app_theme')]
    public function show(Theme $theme): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        $purchasedCourses = [];


        if ($user) {
            foreach ($user->getOrders() as $order) {
                foreach ($order->getOrderItems() as $orderItem) {
                    if ($orderItem->getCourse()) {
                        $purchasedCourses[] = $orderItem->getCourse()->getId();
                    }
                }
            }
        }
        
        return $this->render('theme/index.html.twig', [
            'theme' => $theme,
            'purchasedCourses' => $purchasedCourses,
        ]);
    }
}