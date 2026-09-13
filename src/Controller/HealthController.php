<?php

namespace App\Controller;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Point de contrôle de santé pour la supervision (uptime, load-balancer, Render).
 * Vérifie la connexion à la base PostgreSQL sans exposer de version ni de stack.
 */
final class HealthController extends AbstractController
{
    #[Route('/health', name: 'app_health', methods: ['GET'])]
    public function __invoke(Connection $connection): JsonResponse
    {
        $dbOk = true;
        try {
            $connection->executeQuery('SELECT 1')->fetchOne();
        } catch (Exception) {
            $dbOk = false;
        }

        $status = $dbOk ? 'ok' : 'degraded';
        $code = $dbOk ? Response::HTTP_OK : Response::HTTP_SERVICE_UNAVAILABLE;

        return new JsonResponse([
            'status' => $status,
            'timestamp' => date('c'),
            'checks' => [
                'database' => $dbOk ? 'ok' : 'error',
            ],
        ], $code);
    }
}
