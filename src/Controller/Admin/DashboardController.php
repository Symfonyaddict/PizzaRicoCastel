<?php

namespace App\Controller\Admin;


use App\Repository\UserRepository;
use App\Repository\PizzaRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class DashboardController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_dashboard')]
    public function index(UserRepository $userRepo, PizzaRepository $pizzaRepo): Response
    {
        $users = $userRepo->findAll();
        $pizzas = $pizzaRepo->findAll();
        return $this->render('admin/dashboard.html.twig', [
            'controller_name' => 'DashboardController',
            'users' => $users,
            'pizzas' => $pizzas,
        ]);
    }
}


