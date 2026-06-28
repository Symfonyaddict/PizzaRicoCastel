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
        try {
            // Utilisation de count() pour la performance au lieu de findAll()
            $totalUsers = $userRepo->count([]);
            $totalPizzas = $pizzaRepo->count([]);
            $totalBoissons = $boissonRepo->count([]);
            
            // Statistiques spécifiques pour les pizzas
            $specialPizzas = $pizzaRepo->count(['isSpecial' => true]);
            
            // Calcul du prix moyen des pizzas (exemple de stat utile)
            $pizzas = $pizzaRepo->findAll();
            $avgPrice = 0;
            if ($totalPizzas > 0) {
                $sum = array_reduce($pizzas, fn($carry, $item) => $carry + $item->getPriceLarge(), 0);
                $avgPrice = $sum / $totalPizzas;
            }

            return $this->render('admin/dashboard.html.twig', [
                'totalUsers' => $totalUsers,
                'totalPizzas' => $totalPizzas,
                'totalBoissons' => $totalBoissons,
                'specialPizzas' => $specialPizzas,
                'avgPizzaPrice' => $avgPrice,
            ]);
        } catch (\Exception $e) {
            return new Response("Erreur Dashboard : " . $e->getMessage() . " in " . $e->getFile() . ":" . $e->getLine());
        }
    }
}
