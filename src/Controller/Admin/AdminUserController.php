<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Contrôleur d'administration pour la gestion des utilisateurs.
 */
final class AdminUserController extends AbstractController
{
    /**
     * Affiche la liste de tous les utilisateurs inscrits.
     *
     * @param UserRepository $userRepo Dépôt pour récupérer les données des utilisateurs
     */
    #[Route('/user', name: 'app_admin_user')]
    public function index(UserRepository $userRepo): Response
    {
        // Récupère l'intégralité des utilisateurs depuis la base de données
        $users = $userRepo->findAll();
        
        // Affiche la vue listant les utilisateurs
        return $this->render('Admin/user/index.html.twig', [
            'title' => 'Liste des utilisateurs',
            'user' => $users,
        ]);
    }

    /**
     * Gère la modification d'un utilisateur existant.
     *
     * @param User $user L'utilisateur à modifier, récupéré via son ID dans l'URL
     * @param Request $request Requête HTTP courante
     * @param EntityManagerInterface $em Gestionnaire d'entités pour enregistrer les changements
     */
    #[Route('/user/{id}/edit', name: 'app_admin_user_edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        // Création du formulaire d'édition, pré-rempli avec les données de l'utilisateur
        $form = $this->createForm(UserType::class, $user);
        
        // Traitement des données soumises par le formulaire
        $form->handleRequest($request);
        
        // Si le formulaire est validé sans erreur
        if($form->isSubmitted() && $form->isValid()){
            // Enregistrement des modifications en base de données
            $em->flush();
            
            // Message flash pour informer du succès de la modification
            $this->addFlash('success', 'L\'utilisateur a correctement été modifié');
            
            // Redirection vers la liste des utilisateurs
            return $this->redirectToRoute('app_admin_user');
        }
        
        // Affiche le formulaire de modification
        return $this->render('Admin/user/edit.html.twig', [
            'title' => 'Edition de l\'utilisateur',
            'user' => $user,
            'form' => $form
        ]);
    }

    /**
     * Gère la suppression d'un utilisateur.
     * Note: En production, il est recommandé de vérifier si l'utilisateur ne se supprime pas lui-même
     * ou d'ajouter une vérification CSRF pour des raisons de sécurité.
     *
     * @param User $user L'utilisateur à supprimer
     * @param EntityManagerInterface $em Gestionnaire d'entités pour effectuer la suppression
     */
    #[Route('/user/{id}/delete', name: 'app_admin_user_delete')]
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        // Préparation de la suppression de l'utilisateur
        $em->remove($user);
        // Exécution de la requête de suppression en base de données
        $em->flush();
        
        // Message flash pour confirmer la suppression
        $this->addFlash('success', 'L\'utilisateur a correctement été supprimé');
        
        // Redirection vers la liste des utilisateurs
        return $this->redirectToRoute('app_admin_user');
    }
}
