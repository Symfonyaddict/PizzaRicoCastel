<?php

namespace App\Controller\Admin;

use App\Entity\MentionsLegales;
use App\Form\MentionsLegalesType;
use App\Repository\MentionsLegalesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

/**
 * Administration des mentions légales publiques.
 * Réservé aux utilisateurs ayant le rôle ROLE_ADMIN.
 * Gère un unique enregistrement (upsert) dont le contenu est affiché
 * sur la route publique /mentions-legales.
 */
#[IsGranted('ROLE_ADMIN')]
#[Route('/admin/mentions-legales')]
final class AdminMentionsLegalesController extends AbstractController
{
    #[Route('/', name: 'app_admin_mentions_legales', methods: ['GET', 'POST'])]
    public function index(
        MentionsLegalesRepository $repository,
        Request $request,
        EntityManagerInterface $em,
    ): Response {
        $mentions = $repository->findPublished() ?? new MentionsLegales();
        $form = $this->createForm(MentionsLegalesType::class, $mentions);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if (!$this->isCsrfTokenValid('admin_mentions_legales', (string) $request->request->get('_token'))) {
                $this->addFlash('error', 'Jeton CSRF invalide, la sauvegarde a été refusée.');

                return $this->redirectToRoute('app_admin_mentions_legales', status: Response::HTTP_SEE_OTHER);
            }

            if ($form->isValid()) {
                $mentions->setPageName(MentionsLegales::PAGE_NAME);
                $mentions->setUpdatedAt(new \DateTimeImmutable());
                $em->persist($mentions);
                $em->flush();
                $this->addFlash('success', 'Les mentions légales ont été mises à jour.');

                return $this->redirectToRoute('app_admin_mentions_legales', status: Response::HTTP_SEE_OTHER);
            }

            $this->addFlash('error', 'Le formulaire contient des erreurs, merci de les corriger avant enregistrement.');
        }

        return $this->render('admin/mentions-legales/index.html.twig', [
            'form' => $form->createView(),
            'title' => 'Gestion des mentions légales',
        ]);
    }
}
