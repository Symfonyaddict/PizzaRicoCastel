<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotCompromisedPassword;

/**
 * Formulaire utilisé pour la gestion d'un utilisateur depuis le panneau d'administration.
 */
class UserType extends AbstractType
{
    /**
     * Construit le formulaire pour éditer les informations et les rôles d'un utilisateur.
     *
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email')
            ->add('name')
            ->add('firstname')
            // Champ spécifique pour gérer les rôles, qui est un tableau dans l'entité
            ->add('roles', ChoiceType::class, [
                'choices' => [
                'Utilisateur' => 'ROLE_USER',
                'Administrateur' => 'ROLE_ADMIN'
                ],
                // multiple=true et expanded=true génèrent des cases à cocher (checkboxes)
                'multiple' => true,
                'expanded' => true
            ])
            ->add('password', PasswordType::class, [
                'mapped' => false,
                'required' => false,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new Length(min: 12, max: 4096, minMessage: 'Utilisez au moins {{ limit }} caractères.'),
                    new NotCompromisedPassword(
                        message: 'Ce mot de passe a été divulgué. Choisissez-en un autre.',
                        skipOnError: true,
                    ),
                ],
            ])
            
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
