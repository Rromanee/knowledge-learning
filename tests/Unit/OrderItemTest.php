<?php

namespace App\Tests\Unit;

use App\Entity\Course;
use App\Entity\Lesson;
use App\Entity\Order;
use App\Entity\OrderItem;
use PHPUnit\Framework\TestCase;

class OrderItemTest extends TestCase
{
    // Tests that the order item is initialized with a creation date
    public function testOrderItemIsInitializedWithCreatedAt(): void
    {
        $orderItem = new OrderItem();

        $this->assertNotNull($orderItem->getCreatedAt());
    }

    // Tests that the customer order can be set correctly
    public function testCustomerOrderCanBeSet(): void
    {
        $orderItem = new OrderItem();
        $order = new Order();

        $orderItem->setCustomerOrder($order);

        $this->assertSame($order, $orderItem->getCustomerOrder());
    }

    // Tests that a course can be assigned to the order item
    public function testCourseCanBeSet(): void
    {
        $orderItem = new OrderItem();
        $course = new Course();

        $orderItem->setCourse($course);

        $this->assertSame($course, $orderItem->getCourse());
    }

    // Tests that a lesson can be assigned to the order item
    public function testLessonCanBeSet(): void
    {
        $orderItem = new OrderItem();
        $lesson = new Lesson();

        $orderItem->setLesson($lesson);

        $this->assertSame($lesson, $orderItem->getLesson());
    }

    // Tests that course and lesson can be null
    public function testCourseAndLessonCanBeNull(): void
    {
        $orderItem = new OrderItem();

        $orderItem->setCourse(null);
        $orderItem->setLesson(null);

        $this->assertNull($orderItem->getCourse());
        $this->assertNull($orderItem->getLesson());
    }
}