<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AccountAdressController extends AbstractController
{
    #[Route('/comptes/addresses', name: 'account_adress')]
    public function index(): Response
    {
        return $this->render('account/account_address.html.twig', [

        ]);
    }

    #[Route('/comptes/ajouter-une-addresses', name: 'add_adress')]
    public function add_address(Cart $cart, Request $request, EntityManagerInterface $em): Response
    {
        $address = new Address;


        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());

            $em->persist($address);
            $em->flush();

            if($cart->get()){
                return $this->redirectToRoute('order');
            } else {
                return $this->redirectToRoute('account_adress');
            }


        }

        return $this->render('account/account_address_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/comptes/modifer-une-adresse/{id}', name: 'edit_adress')]
    public function edit_address(Request $request, EntityManagerInterface $em, $id, AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findOneById($id);

        if(!$address || $address->getUser() != $this->getUser()) {

            return $this->redirectToRoute('account_adress');
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());

            $em->persist($address);
            $em->flush();

            return $this->redirectToRoute('account_adress');

        }

        return $this->render('account/account_address_edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/comptes/supprimer/{id}', name: 'remove_adress')]
    public function remove_address(Request $request, EntityManagerInterface $em, $id, AddressRepository $addressRepository): Response
    {
        $address = $addressRepository->findOneById($id);

        if($address && $address->getUser() == $this->getUser()) {
            
            $em->remove($address);
            $em->flush();
        } 


        return $this->redirectToRoute('account_adress');

    }

}
