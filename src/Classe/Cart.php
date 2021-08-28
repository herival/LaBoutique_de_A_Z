<?php

namespace App\Classe;

use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class Cart
{
    private $session;
    private $em;

    public function __construct(SessionInterface $session, EntityManagerInterface $em)
    {
        $this->session = $session;
        $this->em = $em;

    }

    public function add($id)
    {
        $cart = $this->session->get('cart', []);

        if(!empty($cart[$id])){
            $cart[$id]++;
        } else {
            $cart[$id]=1;
        }

        $this->session->set('cart', $cart);

    }

    public function decrease($id)
    {
        $cart = $this->session->get('cart', []);

        if($cart[$id] > 1 ){
            $cart[$id]--;

        }else{
            unset($cart[$id]);
        }

        return $this->session->set('cart', $cart);
    }

    public function get()
    {
        return $this->session->get('cart');
    }

    public function getFull()
    {
        
        $cartComplete = [];

        if($this->get()){
            foreach ($this->get() as $id => $quantity){
                
                $product_object = $this->em->getRepository(Product::class)->findOneById($id);
                
                if(!$product_object){
                    $this->delete($id);
                    continue;
                }
                
                $cartComplete[]= [
                    'product' => $product_object,
                    'quantity' => $quantity
                ];
            }
        }
        return $cartComplete;
    }
}