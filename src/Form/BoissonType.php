<?php

namespace App\Form;

use App\Entity\Boisson;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class BoissonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom de la boisson',
                'attr' => ['placeholder' => 'Ex: Coca-Cola']
            ])
            ->add('category', ChoiceType::class, [
                'label' => 'Catégorie',
                'choices' => [
                    'Sodas' => 'Sodas',
                    'Vins' => 'Vins',
                    'Bières' => 'Bières',
                    'Eaux' => 'Eaux',
                    'Jus de fruits' => 'Jus de fruits',
                    'Boissons Chaudes' => 'Boissons Chaudes',
                ],
                'placeholder' => 'Choisir une catégorie',
                'required' => false,
            ])
            ->add('capacity', TextType::class, [
                'label' => 'Contenance',
                'attr' => ['placeholder' => 'Ex: 33cl, 1.5L'],
                'required' => false,
            ])
            ->add('price', NumberType::class, [
                'label' => 'Prix',
                'attr' => ['placeholder' => 'Ex: 2.50']
            ])
            ->add('imageFile', VichImageType::class, [
                'label' => 'Image',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => 'Supprimer l\'image',
                'download_label' => 'Télécharger',
                'download_uri' => true,
                'image_uri' => true,
                'asset_helper' => true,
                'attr' => ['class' => 'form-control'],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Boisson::class,
        ]);
    }
}
