<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Injecte les en-têtes de sécurité OWASP sur chaque réponse HTTP.
 * - CSP par nonce (sans unsafe-inline ni unsafe-eval)
 * - HSTS activé exclusivement en environnement de production HTTPS
 * - X-Frame-Options DENY, X-Content-Type-Options nosniff
 * - Referrer-Policy strict-origin-when-cross-origin
 * - Permissions-Policy restreint (aucun accès capteurs)
 */
final class SecurityHeadersSubscriber implements EventSubscriberInterface
{
    public function __construct(
        #[Autowire('%kernel.environment%')]
        private readonly string $environment,
    ) {
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        if ($event->isMainRequest()) {
            $event->getRequest()->attributes->set('csp_nonce', bin2hex(random_bytes(16)));
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $response = $event->getResponse();
        $nonce = (string) $request->attributes->get('csp_nonce');

        $cspDirectives = [
            "default-src 'self'",
            "script-src 'nonce-".$nonce."' 'strict-dynamic' https:",
            "style-src 'self' 'nonce-".$nonce."' https://cdn.jsdelivr.net https://fonts.googleapis.com https://cdnjs.cloudflare.com",
            "font-src 'self' https://fonts.gstatic.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com data:",
            "img-src 'self' data: https:",
            "connect-src 'self'",
            "frame-ancestors 'none'",
            "base-uri 'self'",
            "form-action 'self'",
            "object-src 'none'",
        ];
        if ('prod' === $this->environment && $request->isSecure()) {
            $cspDirectives[] = 'upgrade-insecure-requests';
        }
        $response->headers->set('Content-Security-Policy', implode('; ', $cspDirectives));

        $response->headers->set('X-Frame-Options', 'DENY');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'accelerometer=(), camera=(), geolocation=(), gyroscope=(), microphone=(), payment=(), usb=()');

        if ('prod' === $this->environment && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }
    }

    /**
     * @return array<string, array{0: string, 1?: int}>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onKernelRequest', 2048],
            KernelEvents::RESPONSE => ['onKernelResponse', 0],
        ];
    }
}
