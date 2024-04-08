<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class PasswordUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'oldPassword',
                PasswordType::class,
                [
                    'label' => "Mot de passe actuel",
                    'attr' => [
                        'placeholder' => "Indiquez votre mot de passe actuel"
                    ]
                ]
            )
            ->add(
                'newPassword',
                PasswordType::class,
                [
                    'label' => "Nouveau mot de passe",
                    'attr' => [
                        'placeholder' => "Indiquez votre nouveau mot de passe"
                    ]
                ]
            )
            ->add(
                'newPasswordConfirmation',
                PasswordType::class,
                [
                    'label' => "Confirmation du nouveau mot de passe",
                    'attr' => [
                        'placeholder' => "Confirmez ce nouveau mot de passe"
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
