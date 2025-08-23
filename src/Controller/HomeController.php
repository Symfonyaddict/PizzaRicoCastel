<?php

namespace App\Controller;

use App\Repository\PizzaRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(PizzaRepository $pizzaRepository): Response
    {
        $specialPizzas = $pizzaRepository->findBy(['isSpecial' => true]);
        
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
            'specialPizzas' => $specialPizzas,
        ]);
    }
}
