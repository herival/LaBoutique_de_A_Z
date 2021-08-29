<?php

namespace App\Controller;

use DateTime;
use Stripe\Stripe;
use App\Classe\Cart;
use App\Entity\Order;
use App\Form\OrderType;
use App\Entity\OrderDetails;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OrderController extends AbstractController
{
    #[Route('/commande', name: 'order')]
    public function index(Cart $cart, Request $request): Response
    {
        if (!$this->getUser()->getAddresses()->getValues()){
            return $this->redirectToRoute('add_adress');
        }

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]); 


        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cart->getFull()
        ]);
    }
    #[Route('/commande/recapitulatif', name: 'order_recap', methods:'POST')]
    public function add(Cart $cart, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser()
        ]); 

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $date = new DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname().' '.$delivery->getLastname();
            $delivery_content .= '<br/>'.$delivery->getPhone();
            
            if ($delivery->getCompany())
            { 
                $delivery_content .= '<br>'.$delivery->getCompany();
            }
            
            $delivery_content .= '<br/>'.$delivery->getAddress();
            $delivery_content .= '<br/>'.$delivery->getPostal().'-'.$delivery->getCity().'-'.$delivery->getCountry();
            $delivery_content .= '<br/>'.$delivery->getCompany();
            

            // enregistrer ma commande (order)
            $order = new Order();
            $order->setUser($this->getUser());
            $order->setCreatedAt($date);
            $order->setCarrierName($carriers->getName());
            $order->setCarrierPrice($carriers->getPrice());
            $order->setDelivery($delivery_content);
            $order->setIsPaid(0);

            $em->persist($order);

            // enregistrer mes produits
            $product_for_stripe = [];

            foreach ($cart->getFull() as $product) {
                $orderDetails = new OrderDetails();
                // dd($product);

                $orderDetails->setMyOrder($order);
                $orderDetails->setProduct($product['product']->getName());
                $orderDetails->setQuantity($product['quantity']);
                $orderDetails->setPrice($product['product']->getPrice());
                $orderDetails->setTotal($product['product']->getPrice() * $product['quantity']);

                $em->persist($orderDetails);

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

            // $em->flush();

            \Stripe\Stripe::setApiKey('sk_test_51JTmESLKB8SafAqO6ELO7LsCVsJ6lb37f4BkOUDd3ALETuGHa5KTQu09juV5aZW2gb89BsGnEFMR8I4dg0r4eORO00qzr82yOm');  
 

            $YOUR_DOMAIN = 'http://127.0.0.1:8000';

            $checkout_session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    $product_for_stripe
                ],
                'mode' => 'payment',
                'success_url' => 'https://example.com/success',
                'cancel_url' => 'https://example.com/cancel',
              ]);

            header("Location: " . $checkout_session->url);
            // dump($checkout_session->id);
            // dd($checkout_session);

            // $app->post('/create-checkout-session', function (Request $request, Response $response) {
            //     $session = \Stripe\Checkout\Session::create([

            //     ]);

            return $this->render('order/recap.html.twig', [
                'cart' => $cart->getFull(),
                'carrier' => $carriers,
                'delivery' => $delivery_content,
                'stripe_checkout_session' => $checkout_session->id
            ]);

        }
        return $this->redirectToRoute('cart');


    }
}
