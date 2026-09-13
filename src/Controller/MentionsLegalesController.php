<?php

namespace App\Controller;

use App\Repository\MentionsLegalesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Affiche la page publique des mentions légales.
 * Le contenu est systématiquement issu de la base de données (entité MentionsLegales).
 * Aucun texte métier ne doit être en dur dans le template associé.
 */
final class MentionsLegalesController extends AbstractController
{
    #[Route('/mentions-legales', name: 'app_mentions_legales', methods: ['GET'])]
    public function __invoke(MentionsLegalesRepository $repository): Response
    {
        $mentions = $repository->findPublished();

        if (null === $mentions) {
            return $this->render('mentions-legales.html.twig', [
                'title' => 'Mentions légales',
                'content' => '',
                'metaTitle' => 'Mentions légales',
                'metaDescription' => 'Mentions légales du site.',
            ]);
        }

        return $this->render('mentions-legales.html.twig', [
            'title' => $mentions->getTitle(),
            'content' => $mentions->getContent(),
            'metaTitle' => $mentions->getMetaTitle(),
            'metaDescription' => $mentions->getMetaDescription() ?? '',
        ]);
    }
}
