<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/commande/create-session', name: 'stripe_create_session')]
    public function index(Cart $cart)
    {
        $product_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

        foreach ($cart->getFull() as $product) {

            $product_for_stripe[] =[
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                      'name' => $product['product']->getName(),
                    ],
                    'unit_amount' => $product['product']->getPrice(),
                  ],
                  'quantity' => $product['quantity'],
            ];
        }
        dd($product_for_stripe);
        // Stripe::setApiKey('sk_test_51JTmESLKB8SafAqO6ELO7LsCVsJ6lb37f4BkOUDd3ALETuGHa5KTQu09juV5aZW2gb89BsGnEFMR8I4dg0r4eORO00qzr82yOm');  
        $stripe = new StripeClient("sk_test_51JTmESLKB8SafAqO6ELO7LsCVsJ6lb37f4BkOUDd3ALETuGHa5KTQu09juV5aZW2gb89BsGnEFMR8I4dg0r4eORO00qzr82yOm");  
 
        $session = $stripe->checkout->session->create([
        // $checkout_session = \Stripe\Checkout\Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [
                // $product_for_stripe
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                      'name' => 'test',
                    ],
                    'unit_amount' => 200,
                  ],
                  'quantity' => 2,
            ],
            'mode' => 'payment',
            'success_url' => 'https://example.com/success',
            'cancel_url' => 'https://example.com/cancel',
          ]);

        //   dd($checkout_session->id);
          $response = new JsonResponse(['id' => $session->id]);
        //   $response = new JsonResponse(['id' => $checkout_session->id]);
          dump($response);
          return $response;
    }

}
