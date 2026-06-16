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

/**
 * Contrôleur d'administration pour la gestion des pizzas.
 */
final class AdminPizzaController extends AbstractController
{
    /**
     * Affiche la liste de toutes les pizzas dans le panneau d'administration.
     *
     * @param PizzaRepository $pizzaRepo Dépôt pour récupérer les données des pizzas
     */
    #[Route('/admin/pizza', name: 'app_admin_pizza')]
    public function index(PizzaRepository $pizzaRepo): Response
    {
        // Récupération de l'ensemble des pizzas de la base de données
        $pizzas = $pizzaRepo->findAll();
        
        // Rendu de la vue listant les pizzas
        return $this->render('admin/pizza/index.html.twig', [
            'controller_name' => 'AdminPizzaController',
            'pizzas' => $pizzas,
            'title' => 'Gestion des Pizzas',
        ]);
    }

    /**
     * Gère la création d'une nouvelle pizza.
     *
     * @param Request $request Requête HTTP courante
     * @param EntityManagerInterface $em Gestionnaire d'entités pour l'enregistrement
     */
    #[Route('/admin/pizza/create', name: 'app_admin_pizza_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        // Instanciation d'un nouvel objet Pizza
        $pizza = new Pizza();
        
        // Création du formulaire de type PizzaType lié à l'objet $pizza
        $form = $this->createForm(PizzaType::class, $pizza);
        
        // Analyse de la requête pour remplir l'objet $pizza avec les données soumises
        $form->handleRequest($request);
        
        // Si le formulaire est soumis et valide (les contraintes sont respectées)
        if ($form->isSubmitted() && $form->isValid()) {
            // On indique à Doctrine qu'on souhaite sauvegarder cette nouvelle pizza
            $em->persist($pizza);
            // On exécute effectivement la requête SQL d'insertion
            $em->flush();
            
            // Ajout d'un message flash de succès affiché sur la page suivante
            $this->addFlash('success', 'La pizza a correctement été créée');
            
            // Redirection vers la liste des pizzas
            return $this->redirectToRoute('app_admin_pizza');
        }
        
        // Affichage du formulaire de création
        return $this->render('admin/pizza/create.html.twig', [
            'form' => $form->createView(),
            'title' => 'Création d\'une pizza',
        ]);
    }
    
    /**
     * Gère la modification d'une pizza existante.
     *
     * @param PizzaRepository $pizzaRepo Dépôt pour récupérer la pizza par son slug
     * @param string $slug Le slug de la pizza à modifier, issu de l'URL
     * @param Request $request Requête HTTP courante
     * @param EntityManagerInterface $em Gestionnaire d'entités pour enregistrer les modifications
     */
    #[Route('/admin/pizza/edit/{slug}', name: 'app_admin_pizza_edit')]
    public function edit(PizzaRepository $pizzaRepo, string $slug, Request $request, EntityManagerInterface $em): Response
    {
        // Recherche de la pizza correspondant au slug fourni
        $pizza = $pizzaRepo->findOneBy(['slug' => $slug]);
        
        if (!$pizza) {
            throw $this->createNotFoundException('La pizza demandée n\'existe pas');
        }
        
        // Création du formulaire pré-rempli avec les données de la pizza trouvée
        $form = $this->createForm(PizzaType::class, $pizza);
        
        // Analyse de la requête pour récupérer les éventuelles modifications
        $form->handleRequest($request);
        
        // Si les données soumises sont valides
        if ($form->isSubmitted() && $form->isValid()) {
            // Sauvegarde des modifications en base de données (pas besoin de persist() pour une entité déjà gérée)
            $em->flush();
            
            // Message de succès
            $this->addFlash('success', 'La pizza a correctement été modifiée');
            
            // Redirection vers la liste
            return $this->redirectToRoute('app_admin_pizza');
        }
        
        // Affichage du formulaire de modification
        return $this->render('admin/pizza/edit.html.twig', [
            'form' => $form->createView(),
            'title' => 'Modification d\'une pizza',
            'pizza' => $pizza,
        ]);
    }
    
    /**
     * Gère la suppression d'une pizza.
     *
     * @param Pizza $pizza La pizza à supprimer (récupérée automatiquement par son ID)
     * @param EntityManagerInterface $em Gestionnaire d'entités pour la suppression
     */
    #[Route('/admin/pizza/delete/{id}', name: 'app_admin_pizza_delete')]
    public function delete(Pizza $pizza, EntityManagerInterface $em): Response
    {
        // Préparation de la suppression de l'entité
        $em->remove($pizza);
        // Exécution de la suppression
        $em->flush();
        
        // Message de succès
        $this->addFlash('success', 'La pizza a correctement été supprimée');
        
        // Redirection vers la liste des pizzas
        return $this->redirectToRoute('app_admin_pizza');
    }
}
