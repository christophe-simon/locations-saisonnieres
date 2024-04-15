<?php

namespace App\Form;

use App\Entity\Ad;
use App\Entity\User;
use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'rating',
                IntegerType::class,
                [
                    'label' => "Note sur 5",
                    'attr' => [
                        'placeholder' => "Indiquez votre note de 0 à 5",
                        'min' => 0,
                        'max' => 5,
                        'step' => 1
                    ]
                ]
            )
            ->add(
                'content',
                TextareaType::class,
                [
                    'label' => "Commentaire",
                    'attr' => [
                        'placeholder' => "Indiquez votre commentaire"
                    ]
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
