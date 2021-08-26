<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/mon-panier', name: 'cart')]
    public function index(Cart $cart, SessionInterface $session, ProductRepository $productRepository): Response
    {
        $cartComplete = [];

        foreach($session->get('cart') as $id => $quantity){
            $cartComplete[] = [
                'product' => $productRepository->findOneById($id),
                'quantity' => $quantity,
            ];
        }
        
        return $this->render('cart/index.html.twig', [
            'cart' =>  $cartComplete
        ]);
    }

    #[Route('/cart/add/{id}', name: 'add_to_cart')]
    public function add($id, Cart $cart): Response
    {
        
        $cart->add($id);

        // dd($cart);
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove', name: 'remove')]
    public function remove(SessionInterface $session): Response
    {
        $session->remove('cart');
        return $this->redirectToRoute('products');
    }
}
