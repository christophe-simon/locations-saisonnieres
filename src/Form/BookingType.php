<?php

namespace App\Form;

use App\Entity\Booking;
use App\Form\DataTransformer\FrenchToDateTimeTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BookingType extends AbstractType
{
    private $transformer;

    public function __construct(FrenchToDateTimeTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'startsOn',
                TextType::class,
                [
                    'label' => "Date de début de la réservation",
                    'attr' => [
                        'placeholder' => "Indiquez la date à laquelle vous souhaitez arriver"
                    ]
                ]
            )
            ->add(
                'endsOn',
                TextType::class,
                [
                    'label' => "Date de fin de la réservation",
                    'attr' => [
                        'placeholder' => "Indiquez la date à laquelle vous souhaitez partir"
                    ]
                ]
            )
            ->add(
                'comment',
                TextareaType::class,
                [
                    'label' => false,
                    'attr' => [
                        'placeholder' => "Laissez un commentaire à votre hôte si nécessaire"
                    ],
                    'required' => false
                ]
            );

        $builder->get('startsOn')->addModelTransformer($this->transformer);
        $builder->get('endsOn')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Booking::class,
        ]);
    }
}
