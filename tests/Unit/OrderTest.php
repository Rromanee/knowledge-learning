<?php

namespace App\Tests\Unit;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class OrderTest extends TestCase
{
    // Tests that the order is initialized with a creation date and empty items
    public function testOrderIsInitializedWithCreatedAtAndEmptyItems(): void
    {
        $order = new Order();

        $this->assertNotNull($order->getCreatedAt());
        $this->assertCount(0, $order->getOrderItems());
    }

    // Tests that order properties can be set correctly
    public function testOrderPropertiesCanBeSet(): void
    {
        $order = new Order();
        $user = new User();

        $order->setTotal(50.00);
        $order->setStatus('paid');
        $order->setStripeSessionId('cs_test_123');
        $order->setUser($user);

        $this->assertEquals(50.00, $order->getTotal());
        $this->assertEquals('paid', $order->getStatus());
        $this->assertEquals('cs_test_123', $order->getStripeSessionId());
        $this->assertSame($user, $order->getUser());
    }

    // Tests that adding an order item correctly links it to the order
    public function testAddOrderItemLinksItemToOrder(): void
    {
        $order = new Order();
        $orderItem = new OrderItem();

        $order->addOrderItem($orderItem);

        $this->assertCount(1, $order->getOrderItems());
        $this->assertSame($orderItem, $order->getOrderItems()->first());
        $this->assertSame($order, $orderItem->getCustomerOrder());
    }

    // Tests that the same order item cannot be added twice
    public function testSameOrderItemIsNotAddedTwice(): void
    {
        $order = new Order();
        $orderItem = new OrderItem();

        $order->addOrderItem($orderItem);
        $order->addOrderItem($orderItem);

        $this->assertCount(1, $order->getOrderItems());
    }

    // Tests that removing an order item removes it from the order
    public function testRemoveOrderItemRemovesItemFromOrder(): void
    {
        $order = new Order();
        $orderItem = new OrderItem();

        $order->addOrderItem($orderItem);
        $order->removeOrderItem($orderItem);

        $this->assertCount(0, $order->getOrderItems());
        $this->assertNull($orderItem->getCustomerOrder());
    }
}