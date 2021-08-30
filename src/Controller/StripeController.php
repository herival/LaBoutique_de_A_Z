<?php

namespace App\Controller;

use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StripeController extends AbstractController
{
    #[Route('/commande/create-checkout-session/{reference}', name: 'stripe_create_session')]
    public function index(Cart $cart, $reference, EntityManagerInterface $em)
    {
        $order = $em->getRepository(Order::class)->findOneByReference($reference);

        // dd($order->getCarrierPrice() * 100 );
        if(!$order){

            return $this->redirectToRoute('order');

        } 


          $product_for_stripe = [];
          $YOUR_DOMAIN = 'http://127.0.0.1:8000';
  
          foreach ($order->getOrderDetails()->getValues() as $product) {
              
  
              $product_for_stripe[] =[
                  'price_data' => [
                      'currency' => 'eur',
                      'product_data' => [
                        'name' => $product->getProduct(),
                      ],
                      'unit_amount' => $product->getPrice(),
                    ],
                    'quantity' => $product->getQuantity(),
              ];
          }
  
          $product_for_stripe[] =[
            'price_data' => [
                'currency' => 'eur',
                'product_data' => [
                  'name' => $order->getCarrierName(),
                ],
                'unit_amount' => ($order->getCarrierPrice() * 100),
              ],
              'quantity' => 1,
        ];
  
  
          \Stripe\Stripe::setApiKey('sk_test_51JTmESLKB8SafAqO6ELO7LsCVsJ6lb37f4BkOUDd3ALETuGHa5KTQu09juV5aZW2gb89BsGnEFMR8I4dg0r4eORO00qzr82yOm');
  
          $checkout_session = \Stripe\Checkout\Session::create([
              'line_items' => [
                $product_for_stripe
              ],
              'payment_method_types' => [
              'card',
              ],
              'mode' => 'payment',
              'success_url' => $YOUR_DOMAIN . '/commande/merci/{CHECKOUT_SESSION_ID}',
              'cancel_url' => $YOUR_DOMAIN . '/commande/cancel/{CHECKOUT_SESSION_ID}',
          ]);
          
          // dd($checkout_session->url);

          $order->setStripeSessionId($checkout_session->id);

          $em->flush();

          return $this->redirect($checkout_session->url);

    }

}
