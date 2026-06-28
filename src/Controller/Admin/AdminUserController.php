<?php

namespace App\Controller\Admin;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminUserController extends AbstractController
{
    #[Route('/make-me-admin/{email}', name: 'app_make_admin')]
    public function makeAdmin(string $email, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(['email' => $email]);

        if (!$user) {
            return new Response("Utilisateur non trouvé.", 404);
        }

        $user->setRoles(['ROLE_ADMIN']);
        $entityManager->flush();

        return new Response("L'utilisateur " . $email . " est maintenant ROLE_ADMIN ! Vous pouvez maintenant accéder à /admin.");
    }
}
