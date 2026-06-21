<?php

namespace App\Controller\Admin;

use App\Entity\News;
use App\Form\NewsType;
use App\Repository\NewsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/admin/news')]
class AdminNewsController extends AbstractController
{
    #[Route('/', name: 'app_admin_news')]
    public function index(NewsRepository $newsRepo): Response
    {
        return $this->render('admin/news/index.html.twig', [
            'news' => $newsRepo->findBy([], ['createdAt' => 'DESC']),
            'title' => 'Gestion des Actualités'
        ]);
    }

    #[Route('/create', name: 'app_admin_news_create')]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $news = new News();
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($news);
            $em->flush();
            $this->addFlash('success', 'L\'actualité a été créée.');
            return $this->redirectToRoute('app_admin_news');
        }

        return $this->render('admin/news/create.html.twig', [
            'form' => $form->createView(),
            'title' => 'Ajouter une actualité'
        ]);
    }

    #[Route('/edit/{slug}', name: 'app_admin_news_edit')]
    public function edit(News $news, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(NewsType::class, $news);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'L\'actualité a été modifiée.');
            return $this->redirectToRoute('app_admin_news');
        }

        return $this->render('admin/news/edit.html.twig', [
            'form' => $form->createView(),
            'news' => $news,
            'title' => 'Modifier l\'actualité'
        ]);
    }

    #[Route('/delete/{id}', name: 'app_admin_news_delete', methods: ['POST'])]
    public function delete(Request $request, News $news, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete'.$news->getId(), $request->request->get('_token'))) {
            $em->remove($news);
            $em->flush();
            $this->addFlash('success', 'L\'actualité a été supprimée.');
        }
        return $this->redirectToRoute('app_admin_news');
    }
}
