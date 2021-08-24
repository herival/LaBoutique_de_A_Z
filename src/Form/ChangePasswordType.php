<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ChangePasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre email',
                'disabled' => true
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Votre nom',
                'disabled' => true
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Votre prénom',
                'disabled' => true
            ])
            ->add('old_password', PasswordType::class, [
                'mapped'=> false,
                'label' => 'Ancien mot de passe',
                'attr' => [
                    'placeholder' => 'Veuillez saisir votre mot de passe actuel'
                ]
            ])
            ->add('new_password', RepeatedType::class, [
                'mapped' => false,
                'type' => PasswordType::class,
                'invalid_message' => 'Le mot de passe et la confirmation doivent être identique',
                'required' => true,
                'label' => 'Votre mot de passe',
                'first_options' => [ 
                    'label' => 'Mon nouveau de passe', 
                    'attr' => [
                        'placeholder' => 'Merci de saisir un mot de passe'
                    ]
                    ],
                'second_options' => [ 'label' => 'Confirmer votre nouveau mot de passe']
            ])
            ->add('submit', SubmitType::class, [
                'label' => "Modifier",
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
