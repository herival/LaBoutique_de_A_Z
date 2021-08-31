<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeCancelController extends AbstractController
{
    #[Route('/commande/cancel/{stripeSessionId}', name: 'stripe_cancel')]
    public function index($stripeSessionId, OrderRepository $orderRepository): Response
    {
        $order = $orderRepository->findOneByStripeSessionId($stripeSessionId);
        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }
        // envoyer email pour erreur de paiement 
        dump($order);
        return $this->render('stripe_cancel/index.html.twig', [
            'order' => $order
        ]);
    }
}
