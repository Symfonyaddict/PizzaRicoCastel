<?php

namespace App\Controller\Admin;

use App\Entity\SEO;
use App\Form\SEOType;
use App\Repository\SEORepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/seo')]
class AdminSEOController extends AbstractController
{
    #[Route('/', name: 'app_admin_seo', methods: ['GET'])]
    public function index(SEORepository $seoRepo): Response
    {
        return $this->render('admin/seo/index.html.twig', [
            'seos' => $seoRepo->findAll(),
            'title' => 'Gestion du SEO',
        ]);
    }

    #[Route('/edit/{id}', name: 'app_admin_seo_edit', methods: ['GET', 'POST'])]
    public function edit(SEO $seo, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SEOType::class, $seo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Le SEO de la page a été mis à jour.');

            return $this->redirectToRoute('app_admin_seo', status: Response::HTTP_SEE_OTHER);
        }

        if ($form->isSubmitted()) {
            $this->addFlash('error', 'Le formulaire contient des erreurs, merci de les corriger avant enregistrement.');
        }

        return $this->render('admin/seo/edit.html.twig', [
            'form' => $form->createView(),
            'seo' => $seo,
            'title' => 'Modifier le SEO',
        ]);
    }
}
