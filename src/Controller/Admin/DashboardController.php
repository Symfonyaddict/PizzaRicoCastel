<?php

namespace App\Controller\Admin;

use App\Repository\BoissonRepository;
use App\Repository\PizzaRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Tableau de bord principal de l'administration.
 */
#[IsGranted('ROLE_ADMIN')]
final class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard', methods: ['GET'])]
    public function index(UserRepository $userRepo, PizzaRepository $pizzaRepo, BoissonRepository $boissonRepo): Response
    {
        $totalUsers = $userRepo->count([]);
        $totalPizzas = $pizzaRepo->count([]);
        $totalBoissons = $boissonRepo->count([]);

        $specialPizzas = $pizzaRepo->count(['isSpecial' => true]);

        $avgPrice = $pizzaRepo->findAvgPriceLarge() ?? 0.0;

        return $this->render('admin/dashboard.html.twig', [
            'totalUsers' => $totalUsers,
            'totalPizzas' => $totalPizzas,
            'totalBoissons' => $totalBoissons,
            'specialPizzas' => $specialPizzas,
            'avgPizzaPrice' => $avgPrice,
        ]);
    }
}
