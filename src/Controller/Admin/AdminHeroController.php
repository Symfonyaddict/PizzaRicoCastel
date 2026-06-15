<?php

namespace App\Controller\Admin;

use App\Entity\Hero;
use App\Form\HeroType;
use App\Repository\HeroRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur d'administration pour la gestion des sections Hero (bannières).
 * Les routes de cette classe sont préfixées par /admin/hero.
 */
#[Route('/admin/hero')]
final class AdminHeroController extends AbstractController
{
    /**
     * Affiche la liste de tous les éléments Hero.
     *
     * @param HeroRepository $heroRepository Dépôt pour récupérer les données Hero
     */
    #[Route('/', name: 'app_admin_hero')]
    public function index(HeroRepository $heroRepository): Response
    {
        // Récupération de tous les éléments Hero en base de données
        $heroes = $heroRepository->findAll();
        
        // Affichage du template listant les éléments Hero
        return $this->render('admin/hero/index.html.twig', [
            'heroes' => $heroes,
            'title' => 'Gestion de la section Hero'
        ]);
    }
    
    /**
     * Gère la création d'un nouvel élément Hero.
     *
     * @param Request $request Requête HTTP courante (pour récupérer les données du formulaire)
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités pour sauvegarder les données
     */
    #[Route('/create', name: 'app_admin_hero_create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        // Instanciation d'un nouvel objet Hero vide
        $hero = new Hero();
        
        // Création du formulaire de création basé sur l'entité Hero
        $form = $this->createForm(HeroType::class, $hero);
        
        // Traitement de la requête par le formulaire
        $form->handleRequest($request);
        
        // Si le formulaire a été soumis et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On prépare la sauvegarde du nouvel objet en base
            $entityManager->persist($hero);
            // On exécute la requête de sauvegarde
            $entityManager->flush();
            
            // Ajout d'un message flash pour informer l'utilisateur du succès de l'opération
            $this->addFlash('success', 'La section Hero a été créée avec succès.');
            
            // Redirection vers la liste des éléments Hero
            return $this->redirectToRoute('app_admin_hero');
        }
        
        // Si on arrive ici, soit c'est le premier affichage, soit il y a des erreurs dans le formulaire
        return $this->render('admin/hero/create.html.twig', [
            'form' => $form->createView(),
            'title' => 'Création d\'une section Hero'
        ]);
    }
    
    /**
     * Gère la modification d'un élément Hero existant.
     *
     * @param Request $request Requête HTTP courante
     * @param Hero $hero L'élément Hero à modifier (récupéré automatiquement grâce à l'ID dans l'URL)
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités pour sauvegarder les modifications
     */
    #[Route('/edit/{id}', name: 'app_admin_hero_edit')]
    public function edit(Request $request, Hero $hero, EntityManagerInterface $entityManager): Response
    {
        // Création du formulaire de modification avec les données de l'élément Hero existant
        $form = $this->createForm(HeroType::class, $hero);
        
        // Traitement de la requête par le formulaire
        $form->handleRequest($request);
        
        // Si le formulaire a été soumis et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {
            // On exécute la mise à jour en base de données
            // Pas besoin de persist() car l'objet $hero existe déjà en base
            $entityManager->flush();
            
            // Ajout d'un message flash de succès
            $this->addFlash('success', 'La section Hero a été modifiée avec succès.');
            
            // Redirection vers la liste
            return $this->redirectToRoute('app_admin_hero');
        }
        
        // Affichage du formulaire de modification
        return $this->render('admin/hero/edit.html.twig', [
            'form' => $form->createView(),
            'hero' => $hero,
            'title' => 'Modification de la section Hero'
        ]);
    }
    
    /**
     * Gère la suppression d'un élément Hero.
     * Cette route n'accepte que les requêtes POST par sécurité.
     *
     * @param Request $request Requête HTTP courante (pour récupérer le token CSRF)
     * @param Hero $hero L'élément Hero à supprimer
     * @param EntityManagerInterface $entityManager Gestionnaire d'entités pour la suppression
     */
    #[Route('/delete/{id}', name: 'app_admin_hero_delete', methods: ['POST'])]
    public function delete(Request $request, Hero $hero, EntityManagerInterface $entityManager): Response
    {
        // Vérification du token CSRF pour éviter les failles de sécurité (Cross-Site Request Forgery)
        if ($this->isCsrfTokenValid('delete'.$hero->getId(), $request->request->get('_token'))) {
            // Préparation de la suppression
            $entityManager->remove($hero);
            // Exécution de la suppression
            $entityManager->flush();
            
            // Message de confirmation
            $this->addFlash('success', 'La section Hero a été supprimée avec succès.');
        }
        
        // Redirection vers la liste des éléments
        return $this->redirectToRoute('app_admin_hero');
    }
}
