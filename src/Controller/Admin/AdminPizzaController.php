<?php

namespace App\Controller\Admin;

use App\Entity\Pizza;
use App\Form\PizzaType;
use App\Repository\PizzaRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AdminPizzaController extends AbstractController
{
    #[Route('/admin/pizza', name: 'app_admin_pizza')]
    public function index(PizzaRepository $pizzaRepo): Response
    {
        $pizzas = $pizzaRepo->findAll();
        return $this->render('admin/pizza/index.html.twig', [
            'controller_name' => 'AdminPizzaController',
            'pizzas' => $pizzas,
            'title' => 'Gestion des Pizzas',
        ]);
    }

    #[Route('/admin/pizza/create', name: 'app_admin_pizza_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $pizza = new Pizza();
        $form = $this->createForm(PizzaType::class, $pizza);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($pizza);
            $em->flush();
            $this->addFlash('success', 'La pizza a correctement été créée');
            return $this->redirectToRoute('app_admin_pizza');
        }
        return $this->render('admin/pizza/create.html.twig', [
            'form' => $form->createView(),
            'title' => 'Création d\'une pizza',
        ]);
    }
    
    #[Route('/admin/pizza/edit/{slug}', name: 'app_admin_pizza_edit')]
    public function edit(PizzaRepository $pizzaRepo, string $slug, Request $request, EntityManagerInterface $em): Response
    {
        $pizza = $pizzaRepo->findOneBy(['slug' => $slug]);
        // if (!$pizza) {
        //     throw $this->createNotFoundException('La pizza demandée n\'existe pas');
        // }
        $form = $this->createForm(PizzaType::class, $pizza);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'La pizza a correctement été modifiée');
            return $this->redirectToRoute('app_admin_pizza');
        }
        return $this->render('admin/pizza/edit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modification d\'une pizza',
            'pizza' => $pizza,
        ]);
    }
    
    #[Route('/admin/pizza/delete/{id}', name: 'app_admin_pizza_delete')]
    public function delete(Pizza $pizza, EntityManagerInterface $em): Response
    {
        $em->remove($pizza);
        $em->flush();
        $this->addFlash('success', 'La pizza a correctement été supprimée');
        return $this->redirectToRoute('app_admin_pizza');
    }
}
