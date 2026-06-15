<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * Contrôleur gérant l'authentification des utilisateurs (connexion et déconnexion).
 */
class SecurityController extends AbstractController
{
    /**
     * Affiche et gère le formulaire de connexion.
     *
     * @param AuthenticationUtils $authenticationUtils Utilitaire pour récupérer les erreurs et le dernier identifiant saisi
     */
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Récupère l'erreur de connexion s'il y en a eu une lors de la précédente tentative
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupère le dernier nom d'utilisateur (email) saisi par l'utilisateur pour pré-remplir le champ
        $lastUsername = $authenticationUtils->getLastUsername();

        // Rendu de la page de connexion avec les éventuelles erreurs et le dernier identifiant
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    /**
     * Gère la déconnexion de l'utilisateur.
     * Cette méthode est interceptée par le pare-feu de sécurité (firewall) de Symfony.
     */
    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // Cette exception ne sera jamais levée en pratique, car Symfony intercepte la route avant pour déconnecter l'utilisateur
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
