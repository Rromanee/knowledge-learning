<?php

namespace App\Tests\Functions;

use App\Entity\Course;
use App\Entity\Order;
use App\Entity\OrderItem;
use App\Entity\Theme;
use App\Entity\User;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StripeControllerTest extends WebTestCase
{
    /**
     * Tests the complete Stripe payment success flow:
     *
     * 1. Clean the test database
     * 2. Create a test user
     * 3. Create a course in the test database
     * 4. Create a real Stripe Checkout Session in test mode
     * 5. Store the course ID in Stripe metadata
     * 6. Call /stripe/success
     * 7. Verify that an Order is created
     * 8. Verify that an OrderItem is created and linked to the course
     */
    public function testSuccessfulStripePaymentCreatesOrderAndOrderItem(): void
    {
        $client = static::createClient();
        $container = static::getContainer();

        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);

        /** @var Connection $connection */
        $connection = $container->get(Connection::class);

        /*
         * ---------------------------------------------------------
         * 1. Clean the test database
         * ---------------------------------------------------------
         *
         * We temporarily disable foreign key checks because
         * previous test data may contain Orders linked to Users.
         */
        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=0');

        foreach ([
            'order_item',
            '`order`',
            'lesson_validation',
            'certification',
            'lesson',
            'course',
            'theme',
            'user',
        ] as $table) {
            $connection->executeStatement('DELETE FROM ' . $table);
        }

        $connection->executeStatement('SET FOREIGN_KEY_CHECKS=1');

        /*
         * ---------------------------------------------------------
         * 2. Create a test user
         * ---------------------------------------------------------
         */
        $user = new User();

        $user->setEmail('stripe_test@example.com');
        $user->setRoles(['ROLE_CLIENT']);
        $user->setPassword('test-password');
        $user->setIsVerified(true);

        $em->persist($user);

        /*
         * ---------------------------------------------------------
         * 3. Create a theme and a course
         * ---------------------------------------------------------
         *
         * Course requires a Theme because of the non-nullable
         * ManyToOne relationship.
         */
        $theme = new Theme();

        $theme->setTitle('Stripe Test');

        $em->persist($theme);

        $course = new Course();

        $course->setTitle('Cours Stripe Test');
        $course->setPrice(50.00);
        $course->setTheme($theme);

        $em->persist($course);

        $em->flush();

        /*
         * ---------------------------------------------------------
         * 4. Authenticate the test user
         * ---------------------------------------------------------
         */
        $client->loginUser($user);

        /*
         * ---------------------------------------------------------
         * 5. Create a REAL Stripe Checkout Session
         * ---------------------------------------------------------
         *
         * This uses the Stripe TEST secret key from .env.test.
         */
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        $session = Session::create([
            'payment_method_types' => ['card'],

            'line_items' => [
                [
                    'quantity' => 1,

                    'price_data' => [
                        'currency' => 'eur',
                        'unit_amount' => 5000,

                        'product_data' => [
                            'name' => $course->getTitle(),
                        ],
                    ],
                ],
            ],

            'mode' => 'payment',

            /*
             * These are the exact metadata fields used
             * by StripeController.
             */
            'metadata' => [
                'item_id' => (string) $course->getId(),
                'item_type' => 'course',
            ],

            'success_url' =>
                'http://localhost/stripe/success?session_id={CHECKOUT_SESSION_ID}',

            'cancel_url' => 'http://localhost/',
        ]);

        /*
         * ---------------------------------------------------------
         * 6. Verify that Stripe really created the session
         * ---------------------------------------------------------
         */
        $this->assertNotEmpty($session->id);

        $this->assertEquals(
            (string) $course->getId(),
            $session->metadata->item_id
        );

        $this->assertEquals(
            'course',
            $session->metadata->item_type
        );

        /*
         * ---------------------------------------------------------
         * 7. Call the real Symfony success controller
         * ---------------------------------------------------------
         *
         * We simulate the redirect performed by Stripe after
         * successful payment.
         */
        $client->request(
            'GET',
            '/stripe/success?session_id=' . $session->id
        );

        /*
         * The controller should redirect to the purchased course.
         */
        $this->assertResponseRedirects(
            '/course/' . $course->getId()
        );

        /*
         * ---------------------------------------------------------
         * 8. Verify that an Order was created
         * ---------------------------------------------------------
         */
        $em->clear();

        $order = $em
            ->getRepository(Order::class)
            ->findOneBy([
                'stripeSessionId' => $session->id,
            ]);

        $this->assertNotNull($order);

        $this->assertEquals(
            'paid',
            $order->getStatus()
        );

        $this->assertEquals(
            50.00,
            $order->getTotal()
        );

        $this->assertEquals(
            $session->id,
            $order->getStripeSessionId()
        );

        /*
         * Verify that the order belongs to the test user.
         */
        $this->assertNotNull($order->getUser());

        $this->assertEquals(
            'stripe_test@example.com',
            $order->getUser()->getEmail()
        );

        /*
         * ---------------------------------------------------------
         * 9. Verify that an OrderItem was created
         * ---------------------------------------------------------
         */
        $orderItem = $em
            ->getRepository(OrderItem::class)
            ->findOneBy([
                'customerOrder' => $order,
            ]);

        $this->assertNotNull($orderItem);

        /*
         * The purchased item must be the course stored
         * in Stripe metadata.
         */
        $this->assertNotNull($orderItem->getCourse());

        $this->assertEquals(
            $course->getId(),
            $orderItem->getCourse()->getId()
        );

        $this->assertEquals(
            'Cours Stripe Test',
            $orderItem->getCourse()->getTitle()
        );
    }
}