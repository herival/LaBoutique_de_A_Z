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
        if(!empty($session->get('cart'))){
            $cartComplete = [];
            $cart_control = $session->get('cart');

            foreach($cart_control as $id => $quantity){

                $product_object = $productRepository->findOneById($id);

                if(!$product_object){

                    unset($cart_control[$id]);

                    continue;
                }

                $cartComplete[] = [
                    'product' => $product_object,
                    'quantity' => $quantity,
                ];
            }
            
            return $this->render('cart/index.html.twig', [
                'cart' =>  $cartComplete
            ]);
        } else {
            return $this->redirectToRoute('products');
        }

    }

    #[Route('/cart/add/{id}', name: 'add_to_cart')]
    public function add($id, Cart $cart): Response
    {
        
        $cart->add($id);

        // dd($cart);
        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/decrease/{id}', name: 'decrease')]
    public function decrease($id, Cart $cart): Response
    {
        
        $cart->decrease($id);

        return $this->redirectToRoute('cart');
    }

    #[Route('/cart/remove', name: 'remove')]
    public function remove(SessionInterface $session): Response
    {
        $session->remove('cart');
        return $this->redirectToRoute('products');
    }

    #[Route('/cart/delete/{id}', name: 'delete_to_cart')]
    public function delete(SessionInterface $session, $id): Response
    {
        $cart = $session->get('cart');
        $test = 'test';

        unset($cart[$id]);

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart', [
            'test'=> $cart
        ]);
    }
}
