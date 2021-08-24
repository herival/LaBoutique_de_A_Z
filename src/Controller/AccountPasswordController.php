<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    #[Route('/account/password', name: 'account_password')]
    public function index(Request $request, UserPasswordHasherInterface $encoder, EntityManagerInterface $em): Response
    {
        $notification = null;

        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $old_pwd = $form->get('old_password')->getData();
        
            if($encoder->isPasswordValid($user, $old_pwd)){
                $new_pwd = $form->get('new_password')->getData();
                $password = $encoder->hashPassword($user, $new_pwd);

                $user->setPassword($password);

                $em->persist($user);
                $em->flush();

                $notification = "Votre mot de passe a bien été mis à jour.";

            } else {
                $notification = "Votre ancien mot de passe est incorrect";
            }

        }



        return $this->render('account/account_password.html.twig', [
            'form'=> $form->createView(),
            'notification' => $notification
        ]);
    }
}
