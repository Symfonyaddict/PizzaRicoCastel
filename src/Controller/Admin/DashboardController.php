<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use App\Repository\PizzaRepository;
use App\Repository\BoissonRepository;
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
     */
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(UserRepository $userRepo, PizzaRepository $pizzaRepo, BoissonRepository $boissonRepo): Response
    {
        $totalUsers = $userRepo->count([]);
        $totalPizzas = $pizzaRepo->count([]);
        $totalBoissons = $boissonRepo->count([]);

        $specialPizzas = $pizzaRepo->count(['isSpecial' => true]);

        $avgPrice = $pizzaRepo->findAvgPriceLarge();
        if (null === $avgPrice) {
            $avgPrice = 0.0;
        }

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalPizzas' => $totalPizzas,
            'totalBoissons' => $totalBoissons,
            'specialPizzas' => $specialPizzas,
            'avgPizzaPrice' => $avgPrice,
        ]);
    }
}
