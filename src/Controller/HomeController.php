<?php

namespace App\Controller;

use App\Repository\BoissonRepository;
use App\Repository\HeroRepository;
use App\Repository\PizzaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur gérant les pages principales du site destinées aux visiteurs.
 */
final class HomeController extends AbstractController
{
    /**
     * Affiche la page d'accueil avec les pizzas spéciales et le héros (bannière).
     *
     * @param PizzaRepository $pizzaRepo Pour récupérer les pizzas spéciales
     * @param HeroRepository $heroRepo Pour récupérer les informations de la bannière
     */
    #[Route('/', name: 'app_home')]
    public function index(PizzaRepository $pizzaRepo, HeroRepository $heroRepo): Response
    {
        // Récupère les pizzas marquées comme "spéciales", triées par prix croissant
        $specialPizzas = $pizzaRepo->findSpecialOrderByPriceAsc();

        // Récupère la première entrée pour la section héros/bannière
        $hero = $heroRepo->findOneBy([], ['id' => 'ASC']);

        // Rendu de la vue Twig avec les données récupérées
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'specialPizzas' => $specialPizzas,
            'hero' => $hero,
        ]);
    }

    /**
     * Affiche la page listant toutes les pizzas disponibles.
     *
     * @param PizzaRepository $pizzaRepo Pour récupérer l'ensemble des pizzas
     */
    #[Route('/nos-pizzas', name: 'app_pizzas')]
    public function pizzas(PizzaRepository $pizzaRepo): Response
    {
        // Récupère toutes les pizzas de la base de données, triées par prix croissant
        $pizzas = $pizzaRepo->findAllOrderByPriceAsc();

        // Rendu de la vue Twig listant les pizzas
        return $this->render('home/pizzas.html.twig', [
            'controller_name' => 'HomeController',
            'pizzas' => $pizzas,
            'title' => 'Nos Pizzas - Pizza Rico'
        ]);
    }

    /**
     * Affiche la page listant toutes les boissons disponibles.
     *
     * @param BoissonRepository $boissonRepo Pour récupérer l'ensemble des boissons
     */
    #[Route('/nos-boissons', name: 'app_boissons')]
    public function boissons(BoissonRepository $boissonRepo): Response
    {
        // Récupère toutes les boissons de la base de données, triées par prix croissant
        $boissons = $boissonRepo->findAllOrderByPriceAsc();

        // Rendu de la vue Twig listant les boissons
        return $this->render('home/boissons.html.twig', [
            'controller_name' => 'HomeController',
            'boissons' => $boissons,
            'title' => 'Nos Boissons - Pizza Rico'
        ]);
    }
}
