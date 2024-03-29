<?php

namespace App\Form;

use App\Entity\Ad;
use App\Form\PictureType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class AdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'title',
                TextType::class,
                [
                    'label' => "Titre",
                    'attr' => [
                        'placeholder' => "Indiquez un titre pour votre annonce"
                    ]
                ]
            )
            ->add(
                'slug',
                TextType::class,
                [
                    'label' => "Adresse web",
                    'attr' => [
                        'placeholder' => "Indiquez une adresse web pour votre annonce (optionnel)"
                    ],
                    'required' => false,
                ]
            )
            ->add(
                'coverPicture',
                UrlType::class,
                [
                    'label' => "URL de l'image principale",
                    'attr' => [
                        'placeholder' => "Indiquez l'URL d'une image pour votre annonce"
                    ]
                ]
            )
            ->add(
                'introduction',
                TextType::class,
                [
                    'label' => "Courte description",
                    'attr' => [
                        'placeholder' => "Indiquez une courte description pour votre annonce"
                    ]
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'label' => "Description détaillée",
                    'attr' => [
                        'placeholder' => "Indiquez une description détaillée pour votre annonce"
                    ]
                ]
            )
            ->add(
                'rooms',
                IntegerType::class,
                [
                    'label' => "Nombre de chambres",
                    'attr' => [
                        'placeholder' => "Indiquez un nombre de chambres pour votre annonce"
                    ]
                ]
            )
            ->add(
                'price',
                MoneyType::class,
                [
                    'label' => "Prix (à la nuitée)",
                    'attr' => [
                        'placeholder' => "Indiquez un prix pour votre annonce"
                    ]
                ]
            )
            ->add(
                'pictures',
                CollectionType::class,
                [
                    'entry_type' => PictureType::class,
                    'allow_add' => true,
                    'allow_delete' => true
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
