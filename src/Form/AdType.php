<?php

namespace App\Form;

use App\Entity\Ad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class AdType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre'
            ])
            ->add('slug', TextType::class, [
                'label' => 'Adresse web de l\'annonce'
            ])
            ->add('coverPicture', UrlType::class, [
                'label' => 'URL de l\'image principale'
            ])
            ->add('introduction', TextType::class, [
                'label' => 'Introduction'
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Description détaillée'
            ])
            ->add('rooms', IntegerType::class, [
                'label' => 'Nombre de chambres'
            ])
            ->add('price', MoneyType::class, [
                'label' => 'Prix (à la nuitée)'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ad::class,
        ]);
    }
}
