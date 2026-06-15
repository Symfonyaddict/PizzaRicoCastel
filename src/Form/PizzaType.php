<?php

namespace App\Form;

use App\Entity\Pizza;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

/**
 * Formulaire utilisé pour la création et la modification d'une Pizza.
 */
class PizzaType extends AbstractType
{
    /**
     * Construit le formulaire en ajoutant les champs correspondant aux propriétés de l'entité Pizza.
     *
     * @param FormBuilderInterface $builder L'outil de construction du formulaire
     * @param array $options Options supplémentaires passées au formulaire
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('ingredient')
            // Champ pour l'upload d'image géré par VichUploader
            ->add('imageFile', VichImageType::class, [
                
                'label' => 'Image',
                'required' => false, // Permet de ne pas avoir à re-uploader l'image lors d'une simple modification de texte
                'allow_delete' => true,
                'delete_label' => 'Supprimer l\'image',
                'download_label' => 'Télécharger l\'image',
                'download_uri' => true,
                'image_uri' => true, // Permet l'affichage de l'image actuellement associée
                'asset_helper' => true,
                'attr' => ['class' => 'form-control'],
            ])
            ->add('priceMedium')
            ->add('priceLarge')
            ->add('isSpecial')
        ;
    }

    /**
     * Configure les options globales du formulaire.
     * Associe notamment ce formulaire à l'entité Pizza.
     *
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Pizza::class,
        ]);
    }
}
