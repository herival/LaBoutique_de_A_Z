<?php

namespace App\Controller;

use App\Entity\Address;
use App\Form\AddressType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAdressController extends AbstractController
{
    #[Route('/comptes/addresses', name: 'account_adress')]
    public function index(): Response
    {
        return $this->render('account/account_address.html.twig', [

        ]);
    }

    #[Route('/comptes/ajouter-une-addresses', name: 'add_adress')]
    public function add_address(Request $request, EntityManagerInterface $em): Response
    {
        $address = new Address;


        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());

            $em->persist($address);
            $em->flush();

            return $this->redirectToRoute('account_adress');

        }

        return $this->render('account/account_address_add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
