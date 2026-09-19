<?php

namespace App\Form;

use App\Entity\MentionsLegales;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire d'administration pour éditer les mentions légales.
 * Tous les champs correspondent aux données persistées en base (MentionsLegales).
 */
class MentionsLegalesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('metaTitle', TextType::class, [
                'label' => 'Titre SEO (balise <title>)',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Mentions légales — Pizza Rico Castel-Sarrasin',
                    'maxlength' => 255,
                ],
            ])
            ->add('metaDescription', TextType::class, [
                'label' => 'Meta description SEO',
                'required' => false,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Courte description affichée dans les résultats de recherche.',
                    'maxlength' => 255,
                ],
            ])
            ->add('title', TextType::class, [
                'label' => 'Titre affiché sur la page',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Mentions légales',
                    'maxlength' => 255,
                ],
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Contenu des mentions légales',
                'attr' => [
                    'class' => 'form-control',
                    'rows' => 16,
                    'placeholder' => 'Éditeur responsable, hébergeur, propriété intellectuelle, données personnelles, contact…',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => MentionsLegales::class,
        ]);
    }
}
