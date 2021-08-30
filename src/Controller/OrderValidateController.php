<?php

namespace App\Controller;

use App\Repository\OrderRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderValidateController extends AbstractController
{
    #[Route('/commande/merci/{stripeSessionId}', name: 'order_validate')]
    public function index($stripeSessionId, OrderRepository $orderRepository, EntityManagerInterface $em): Response
    {

        $order = $orderRepository->findOneByStripeSessionId($stripeSessionId);

        if(!$order || $order->getUser() != $this->getUser()){
            return $this->redirectToRoute('home');
        }

        // Modifier le status isPaid
        if(!$order->getIsPaid()){
            $order->setIsPaid(1);
            $em->flush();
        }


        // Envoyer le mail

        // Afficher les informations de la commande


        return $this->render('order_validate/index.html.twig', [
            'order' => $order
        ]); 
    }
}
