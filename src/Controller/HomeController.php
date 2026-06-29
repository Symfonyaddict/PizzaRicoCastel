<?php

namespace App\Controller;

use App\Repository\AboutRepository;
use App\Repository\BoissonRepository;
use App\Repository\HeroRepository;
use App\Repository\NewsRepository;
use App\Repository\PizzaRepository;
use App\Repository\SEORepository;
use App\Entity\News;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur gérant les pages principales du site destinées aux visiteurs.
 */
final class HomeController extends AbstractController
{
    /**
     * Affiche la page d'accueil avec les pizzas spéciales, le héros, À propos et Actualités.
     */
    #[Route('/', name: 'app_home')]
    public function index(
        PizzaRepository $pizzaRepo, 
        HeroRepository $heroRepo, 
        AboutRepository $aboutRepo, 
        NewsRepository $newsRepo,
        SEORepository $seoRepo
    ): Response {
        $specialPizzas = $pizzaRepo->findSpecialOrderByPriceAsc();
        $hero = $heroRepo->findOneBy([], ['id' => 'ASC']);
        $about = $aboutRepo->findOneBy([]);
        $news = $newsRepo->findBy([], ['createdAt' => 'DESC'], 3);
        $seo = $seoRepo->findOneBy(['pageName' => 'home']);

        return $this->render('home/index.html.twig', [
            'specialPizzas' => $specialPizzas,
            'hero' => $hero,
            'about' => $about,
            'news' => $news,
            'seo' => $seo,
        ]);
    }

    /**
     * Affiche la page listant toutes les pizzas disponibles.
     */
    #[Route('/nos-pizzas', name: 'app_pizzas')]
    public function pizzas(PizzaRepository $pizzaRepo, SEORepository $seoRepo): Response
    {
        $pizzas = $pizzaRepo->findAllOrderByPriceAsc();
        $seo = $seoRepo->findOneBy(['pageName' => 'pizzas']);

        return $this->render('home/pizzas.html.twig', [
            'pizzas' => $pizzas,
            'title' => 'Nos Pizzas - Pizza Rico',
            'seo' => $seo,
        ]);
    }

    /**
     * Affiche la page listant toutes les boissons disponibles.
     */
    #[Route('/nos-boissons', name: 'app_boissons')]
    public function boissons(BoissonRepository $boissonRepo, SEORepository $seoRepo): Response
    {
        $boissons = $boissonRepo->findAllOrderByPriceAsc();
        $seo = $seoRepo->findOneBy(['pageName' => 'boissons']);

        return $this->render('home/boissons.html.twig', [
            'boissons' => $boissons,
            'title' => 'Nos Boissons - Pizza Rico',
            'seo' => $seo,
        ]);
    }

    /**
     * Affiche le détail d'une actualité.
     */
    #[Route('/actualite/{slug}', name: 'app_news_detail', methods: ['GET'])]
    public function newsDetail(News $news): Response
    {
        return $this->render('home/news_detail.html.twig', [
            'item' => $news,
            'title' => $news->getTitle() . ' - Pizza Rico'
        ]);
    }

    /**
     * Affiche la page "À propos" complète.
     */
    #[Route('/a-propos', name: 'app_about')]
    public function about(AboutRepository $aboutRepo, SEORepository $seoRepo): Response
    {
        $about = $aboutRepo->findOneBy([]);
        $seo = $seoRepo->findOneBy(['pageName' => 'about']);

        return $this->render('home/about.html.twig', [
            'about' => $about,
            'title' => 'À Propos de nous - Pizza Rico',
            'seo' => $seo,
        ]);
    }

    /**
     * Affiche la liste de toutes les actualités.
     */
    #[Route('/actualites', name: 'app_news')]
    public function news(NewsRepository $newsRepo, SEORepository $seoRepo): Response
    {
        $news = $newsRepo->findBy([], ['createdAt' => 'DESC']);
        $seo = $seoRepo->findOneBy(['pageName' => 'news']);

        return $this->render('home/news.html.twig', [
            'news' => $news,
            'title' => 'Nos Actualités - Pizza Rico',
            'seo' => $seo,
        ]);
    }
}
