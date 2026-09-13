<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\FlashBagAwareSessionInterface;
use Symfony\Component\RateLimiter\RateLimiterFactory;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\TooManyLoginAttemptsAuthenticationException;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Event\CheckPassportEvent;
use Symfony\Component\Security\Http\Event\LoginFailureEvent;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

/**
 * Applique les politiques de rate-limiting configurées dans rate_limiter.yaml :
 * - login_attempts : 5 échecs / minute / IP
 * - register_attempts : 3 requêtes / minute / IP (appliquée côté RegistrationController)
 *
 * On ne consomme le quota qu'en cas d'échec avéré, on libère l'IP après un succès.
 */
final class LoginAttemptsSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly RequestStack $requestStack,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly RateLimiterFactory $loginAttemptsLimiter,
    ) {
    }

    public function onCheckPassport(CheckPassportEvent $event): void
    {
        $passport = $event->getPassport();
        if (!$passport->hasBadge(UserBadge::class)) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        $ip = (string) $request->getClientIp();
        $limiter = $this->loginAttemptsLimiter->create($ip);

        if (!$limiter->consume(0)->isAccepted()) {
            $retryAfter = $limiter->consume(0)->getRetryAfter();
            $seconds = max(1, $retryAfter->getTimestamp() - time());

            throw new TooManyLoginAttemptsAuthenticationException($seconds);
        }
    }

    public function onLoginFailure(LoginFailureEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        $ip = (string) $request->getClientIp();
        $this->loginAttemptsLimiter->create($ip)->consume(1);

        $session = $request->hasSession() ? $request->getSession() : null;
        if ($session instanceof FlashBagAwareSessionInterface) {
            $isRateLimited = $event->getException() instanceof TooManyLoginAttemptsAuthenticationException;
            $message = $isRateLimited
                ? 'Trop de tentatives de connexion. Réessayez dans une minute.'
                : 'Identifiants invalides. Vérifiez votre adresse email et votre mot de passe.';
            $session->getFlashBag()->add('error', $message);
        }

        $response = new RedirectResponse(
            $this->urlGenerator->generate('app_login'),
            Response::HTTP_SEE_OTHER
        );
        $event->setResponse($response);
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        $ip = (string) $request->getClientIp();
        $this->loginAttemptsLimiter->create($ip)->reset();
    }

    /**
     * @return array<string, array{0: string, 1?: int}|string>
     */
    public static function getSubscribedEvents(): array
    {
        return [
            CheckPassportEvent::class => ['onCheckPassport', 10],
            LoginFailureEvent::class => ['onLoginFailure', 0],
            LoginSuccessEvent::class => ['onLoginSuccess', 0],
        ];
    }
}
