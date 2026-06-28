<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Extension Twig personnalisée pour gérer les liens actifs dans la navigation.
 * Permet d'ajouter facilement une classe CSS "active" sur le lien de la page courante.
 */
class ActiveLinkExtension extends AbstractExtension
{
    /**
     * @var RequestStack Permet d'accéder à la requête HTTP courante
     */
    private $requestStack;

    /**
     * Constructeur de l'extension.
     * L'injection de dépendances de Symfony fournit automatiquement le RequestStack.
     *
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * Déclare les filtres Twig personnalisés fournis par cette extension.
     *
     * @return array Liste des filtres Twig
     */
    public function getFilters(): array
    {
        return [
            // Déclare un filtre utilisable dans les templates via {{ 'nom_de_la_route'|is_active_route }}
            new TwigFilter('is_active_route', [$this, 'isActiveRoute']),
        ];
    }

    /**
     * Vérifie si la route passée en paramètre correspond à la route courante.
     * Retourne la chaîne 'active' si c'est le cas, ce qui est utile pour les classes CSS.
     *
     * @param string $routeName Le nom de la route à tester
     * @return string 'active' si la route correspond, une chaîne vide sinon
     */
    public function isActiveRoute(string $routeName): string
    {
        $request = $this->requestStack->getCurrentRequest();
        if (!$request) {
            return '';
        }

        // Récupère le nom de la route de la requête actuelle
        $currentRoute = $request->attributes->get('_route');
        
        // Compare avec la route testée et retourne la classe CSS appropriée
        return $currentRoute === $routeName ? 'active' : '';
    }
}