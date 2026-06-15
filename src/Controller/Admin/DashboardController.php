<?php

namespace App\Controller\Admin;


use App\Repository\UserRepository;
use App\Repository\PizzaRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * Contrôleur gérant le tableau de bord principal de l'administration.
 */
final class DashboardController extends AbstractController
{
    /**
     * Affiche la page d'accueil de l'interface d'administration (Dashboard).
     * Fournit une vue d'ensemble avec quelques statistiques (utilisateurs, pizzas).
     *
     * @param UserRepository $userRepo Dépôt pour accéder aux données des utilisateurs
     * @param PizzaRepository $pizzaRepo Dépôt pour accéder aux données des pizzas
     */
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(UserRepository $userRepo, PizzaRepository $pizzaRepo): Response
    {
        // Récupère tous les utilisateurs inscrits
        $users = $userRepo->findAll();
        // Récupère toutes les pizzas disponibles
        $pizzas = $pizzaRepo->findAll();
        
        // Rendu de la vue du tableau de bord avec les données globales
        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'DashboardController',
            'users' => $users,
            'pizzas' => $pizzas,
        ]);
    }
}
