<?php

namespace App\Controller;

use App\Repository\MentionsLegalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Affiche la page publique des mentions légales.
 * L'ensemble des données est issu de l'entité MentionsLegales (base de données).
 * Le contrôleur ne fournit aucun texte métier par défaut ; le template affiche
 * un état vide invitant à renseigner le contenu depuis le backoffice.
 */
final class MentionsLegalesController extends AbstractController
{
    #[Route('/mentions-legales', name: 'app_mentions_legales', methods: ['GET'])]
    public function __invoke(MentionsLegalesRepository $repository): Response
    {
        $mentions = $repository->findPublished();

        return $this->render('mentions-legales.html.twig', [
            'title' => $mentions?->getTitle() ?? '',
            'content' => $mentions?->getContent() ?? '',
            'metaTitle' => $mentions?->getMetaTitle() ?? '',
            'metaDescription' => $mentions?->getMetaDescription() ?? '',
        ]);
    }
}
