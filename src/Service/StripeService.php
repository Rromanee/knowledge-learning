<?php

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Service responsible for creating Stripe Checkout sessions.
 * Handles both course and lesson purchases.
 */
class StripeService
{
    /**
     * Creates a Stripe Checkout session for a course or lesson purchase.
     *
     * @param string                $name         Product display name
     * @param float                 $price        Price in euros (converted to cents internally)
     * @param int                   $itemId       ID of the course or lesson
     * @param string                $itemType     Either 'course' or 'lesson'
     * @param string                $successRoute Symfony route name for success redirect
     * @param UrlGeneratorInterface $urlGenerator Used to generate absolute URLs
     * @return Session The created Stripe session
     */
    public function createCheckoutSession(
        string $name,
        float $price,
        int $itemId,
        string $itemType,
        string $successRoute,
        UrlGeneratorInterface $urlGenerator
    ): Session {
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);

        return Session::create([
            'payment_method_types' => ['card'],

            'line_items' => [[
                'quantity' => 1,

                'price_data' => [
                    'currency' => 'eur',

                    'unit_amount' => (int) ($price * 100),

                    'product_data' => [
                        'name' => $name,
                    ],
                ],
            ]],

            'mode' => 'payment',

            'metadata' => [
                'item_id' => $itemId,
                'item_type' => $itemType,
            ],

            'success_url' => $urlGenerator->generate(
                $successRoute,
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            ) . '?session_id={CHECKOUT_SESSION_ID}',

            'cancel_url' => $urlGenerator->generate(
                'app_home',
                [],
                UrlGeneratorInterface::ABSOLUTE_URL
            ),
        ]);
    }
}