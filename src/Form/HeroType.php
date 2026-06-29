<?php

namespace App\Form;

use App\Entity\Hero;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Formulaire utilisé pour la création et la modification de la section Hero (bannière).
 */
class HeroType extends AbstractType
{
    /**
     * Construit le formulaire en ajoutant les différents champs nécessaires.
     *
     * @param FormBuilderInterface $builder L'outil de construction du formulaire
     * @param array $options Options supplémentaires passées au formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre principal',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Les meilleures pizzas artisanales'
                ]
            ])
            ->add('subTitle', null, [
                'label' => 'Sous-titre',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Ex: Découvrez notre savoir-faire traditionnel'
                ]
            ])
            // Champ spécifique pour l'upload d'image géré par VichUploader
            ->add('backgroundFile', VichImageType::class, [
                'label' => 'Image de fond',
                'required' => false, // Non obligatoire lors de l'édition si l'image existe déjà
                'allow_delete' => true, // Permet de supprimer l'image existante
                'delete_label' => 'Supprimer l\'image',
                'download_label' => 'Télécharger l\'image',
                'download_uri' => true,
                'image_uri' => true, // Affiche un aperçu de l'image actuelle
                'asset_helper' => true,
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Choisir une image de fond'
                ],
            ])
        ;
    }

    /**
     * Configure les options par défaut pour ce type de formulaire.
     *
     * @param OptionsResolver $resolver L'outil de résolution des options
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Hero::class,
        ]);
    }
}
