<?php

namespace App\Controller\Admin;

use App\Entity\Boisson;
use App\Form\BoissonType;
use App\Repository\BoissonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Contrôleur d'administration pour la gestion des boissons.
 */
final class AdminBoissonController extends AbstractController
{
    /**
     * Affiche la liste de toutes les boissons.
     */
    #[Route('/admin/boisson', name: 'app_admin_boisson')]
    public function index(BoissonRepository $boissonRepo): Response
    {
        $boissons = $boissonRepo->findAll();
        
        return $this->render('admin/boisson/index.html.twig', [
            'boissons' => $boissons,
            'title' => 'Gestion des Boissons',
        ]);
    }

    /**
     * Gère la création d'une nouvelle boisson.
     */
    #[Route('/admin/boisson/create', name: 'app_admin_boisson_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $boisson = new Boisson();
        $form = $this->createForm(BoissonType::class, $boisson);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($boisson);
            $em->flush();
            
            $this->addFlash('success', 'La boisson a été créée avec succès');
            return $this->redirectToRoute('app_admin_boisson');
        }
        
        return $this->render('admin/boisson/create.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajouter une boisson',
        ]);
    }

    /**
     * Gère la modification d'une boisson.
     */
    #[Route('/admin/boisson/edit/{slug}', name: 'app_admin_boisson_edit')]
    public function edit(BoissonRepository $boissonRepo, string $slug, Request $request, EntityManagerInterface $em): Response
    {
        $boisson = $boissonRepo->findOneBy(['slug' => $slug]);
        
        if (!$boisson) {
            throw $this->createNotFoundException('La boisson demandée n\'existe pas');
        }
        
        $form = $this->createForm(BoissonType::class, $boisson);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            
            $this->addFlash('success', 'La boisson a été modifiée avec succès');
            return $this->redirectToRoute('app_admin_boisson');
        }
        
        return $this->render('admin/boisson/edit.html.twig', [
            'form' => $form->createView(),
            'boisson' => $boisson,
            'title' => 'Modifier la boisson',
        ]);
    }

    /**
     * Gère la suppression d'une boisson.
     */
    #[Route('/admin/boisson/delete/{id}', name: 'app_admin_boisson_delete', methods: ['POST', 'DELETE', 'GET'])]
    public function delete(Boisson $boisson, EntityManagerInterface $em): Response
    {
        $em->remove($boisson);
        $em->flush();
        
        $this->addFlash('success', 'La boisson a été supprimée avec succès');
        return $this->redirectToRoute('app_admin_boisson');
    }
}
