<?php

namespace App\Controller\Admin;

use App\Entity\About;
use App\Form\AboutType;
use App\Repository\AboutRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/about')]
class AdminAboutController extends AbstractController
{
    #[Route('/', name: 'app_admin_about')]
    public function index(AboutRepository $aboutRepo, Request $request, EntityManagerInterface $em): Response
    {
        $about = $aboutRepo->findOneBy([]) ?? new About();
        $form = $this->createForm(AboutType::class, $about);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($about);
            $em->flush();
            $this->addFlash('success', 'La section "À propos" a été mise à jour.');
            return $this->redirectToRoute('app_admin_about');
        }

        return $this->render('admin/about/index.html.twig', [
            'form' => $form->createView(),
            'title' => 'Gestion de la section À propos'
        ]);
    }
}
