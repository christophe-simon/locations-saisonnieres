<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PersonalDataUpdateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'firstName',
                TextType::class,
                [
                    'label' => "Prénom",
                    'attr' => [
                        'placeholder' => "Indiquez votre prénom"
                    ],
                    'required' => false
                ]
            )
            ->add(
                'lastName',
                TextType::class,
                [
                    'label' => "Nom",
                    'attr' => [
                        'placeholder' => "Indiquez votre nom de famille"
                    ]
                ]
            )
            ->add(
                'email',
                EmailType::class,
                [
                    'label' => "Email",
                    'attr' => [
                        'placeholder' => "Indiquez votre adresse email"
                    ]
                ]
            )
            // ->add('roles')
            ->add(
                'picture',
                UrlType::class,
                [
                    'label' => "Photo de profil",
                    'attr' => [
                        'placeholder' => "Indiquez l'URL de votre photo de profil"
                    ],
                    'required' => false
                ]
            )
            ->add(
                'introduction',
                TextType::class,
                [
                    'label' => "Courte description",
                    'attr' => [
                        'placeholder' => "Présentez-vous en quelques mots"
                    ]
                ]
            )
            ->add(
                'description',
                TextareaType::class,
                [
                    'label' => "Description détaillée",
                    'attr' => [
                        'placeholder' => "Présentez-vous en détails"
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
