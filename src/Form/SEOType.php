<?php

namespace App\Form;

use App\Entity\SEO;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SEOType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('pageName', TextType::class, [
                'label' => 'Nom de la page (identifiant interne)',
                'attr' => ['class' => 'form-control', 'readonly' => true]
            ])
            ->add('metaTitle', TextType::class, [
                'label' => 'Meta Title (Balise Titre)',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Pizza Rico | Meilleure pizzeria de Castelsarrasin'
                ]
            ])
            ->add('metaDescription', TextareaType::class, [
                'label' => 'Meta Description',
                'attr' => [
                    'class' => 'form-control', 
                    'rows' => 3,
                    'placeholder' => 'Décrivez le contenu de la page en 160 caractères environ...'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => SEO::class,
        ]);
    }
}
