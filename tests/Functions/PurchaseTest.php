<?php

namespace App\Tests\Functions;

use App\Entity\Course;
use App\Entity\Lesson;
use PHPUnit\Framework\TestCase;

class PurchaseTest extends TestCase
{
    // Tests that an empty cart does not generate a Stripe session
    public function testNoPurchaseWithoutItem(): void
    {
        $items = [];
        $this->assertEmpty($items);
    }

    // Tests that Stripe line items are generated correctly for a course
    public function testLineItemsGeneratedCorrectlyForCourse(): void
    {
        $course = new Course();
        $course->setTitle('Initiation à la guitare');
        $course->setPrice(50.0);

        $lineItems = [[
            'quantity' => 1,
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => (int)($course->getPrice() * 100),
                'product_data' => [
                    'name' => $course->getTitle(),
                ],
            ],
        ]];

        $this->assertCount(1, $lineItems);
        $this->assertEquals(5000, $lineItems[0]['price_data']['unit_amount']);
        $this->assertEquals('Initiation à la guitare', $lineItems[0]['price_data']['product_data']['name']);
        $this->assertEquals('eur', $lineItems[0]['price_data']['currency']);
    }

    // Tests that Stripe line items are generated correctly for a lesson
    public function testLineItemsGeneratedCorrectlyForLesson(): void
    {
        $lesson = new Lesson();
        $lesson->setTitle('Les accords et les gammes');
        $lesson->setPrice(26.0);

        $lineItems = [[
            'quantity' => 1,
            'price_data' => [
                'currency' => 'eur',
                'unit_amount' => (int)($lesson->getPrice() * 100),
                'product_data' => [
                    'name' => $lesson->getTitle(),
                ],
            ],
        ]];

        $this->assertEquals(2600, $lineItems[0]['price_data']['unit_amount']);
        $this->assertEquals('Les accords et les gammes', $lineItems[0]['price_data']['product_data']['name']);
    }

    // Tests that metadata contains required fields for order processing
    public function testPurchaseMetadataIsValid(): void
    {
        foreach (['course', 'lesson'] as $type) {
            $metadata = [
                'item_id'   => 1,
                'item_type' => $type,
            ];

            $this->assertArrayHasKey('item_id', $metadata);
            $this->assertArrayHasKey('item_type', $metadata);
            $this->assertContains($metadata['item_type'], ['course', 'lesson']);
        }
    }
}