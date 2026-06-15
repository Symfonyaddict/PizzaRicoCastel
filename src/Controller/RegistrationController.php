<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur gérant l'inscription de nouveaux utilisateurs.
 */
class RegistrationController extends AbstractController
{
    /**
     * Affiche et traite le formulaire d'inscription.
     *
     * @param Request $request La requête HTTP contenant les données du formulaire
     * @param UserPasswordHasherInterface $userPasswordHasher Service pour hacher le mot de passe
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités pour sauvegarder l'utilisateur en base
     */
    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        // Création d'une nouvelle instance de l'entité User
        $user = new User();
        
        // Création du formulaire lié à l'entité User
        $form = $this->createForm(RegistrationFormType::class, $user);
        
        // Inspection de la requête pour remplir le formulaire avec les données soumises
        $form->handleRequest($request);

        // Vérifie si le formulaire a été soumis et si les données sont valides selon les contraintes
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var string $plainPassword */
            // Récupération du mot de passe en clair depuis le formulaire
            $plainPassword = $form->get('plainPassword')->getData();

            // Hachage du mot de passe en clair avant de le stocker dans l'entité
            $user->setPassword($userPasswordHasher->hashPassword($user, $plainPassword));

            // Préparation de la sauvegarde de l'utilisateur en base de données
            $entityManager->persist($user);
            
            // Exécution de la requête d'insertion (sauvegarde effective en base de données)
            $entityManager->flush();

            // Ici, on pourrait ajouter d'autres actions, comme l'envoi d'un email de confirmation

            // Redirection vers la page d'accueil après une inscription réussie
            return $this->redirectToRoute('app_home');
        }

        // Si le formulaire n'est pas soumis ou contient des erreurs, on affiche la page d'inscription
        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }
}
