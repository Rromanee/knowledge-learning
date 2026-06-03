<?php

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class StripeService
{
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