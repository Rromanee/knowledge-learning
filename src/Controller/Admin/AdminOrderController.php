<?php

namespace App\Controller\Admin;

use App\Entity\Order;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/** Displays orders in the backoffice. */
final class AdminOrderController extends AbstractController
{
    #[Route('/admin/orders', name: 'app_admin_orders')]
    public function orders(
        OrderRepository $orderRepository
    ): Response {
        return $this->render('admin/orders.html.twig', [
            'orders' => $orderRepository->findAll(),
        ]);
    }

    #[Route('/admin/orders/{id}', name: 'app_admin_order')]
    public function show(
        Order $order
    ): Response {
        return $this->render('admin/order.html.twig', [
            'order' => $order,
        ]);
    }
}