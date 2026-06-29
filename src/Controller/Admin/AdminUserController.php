<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

class AdminUserController extends AbstractController
{
    #[Route('/admin/user', name: 'app_admin_user')]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('admin/user/index.html.twig', [
            'user' => $userRepository->findAll(),
            'title' => 'Gestion des Utilisateurs',
        ]);
    }

    #[Route('/admin/user/edit/{id}', name: 'app_admin_user_edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            if ($plainPassword) {
                $user->setPassword($passwordHasher->hashPassword($user, $plainPassword));
            }

            $entityManager->flush();
            $this->addFlash('success', 'Utilisateur modifié avec succès.');

            return $this->redirectToRoute('app_admin_user');
        }

        return $this->render('admin/user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
            'title' => 'Modifier l\'utilisateur',
        ]);
    }

    #[Route('/admin/user/delete/{id}', name: 'app_admin_user_delete', methods: ['POST', 'GET'])]
    public function delete(User $user, EntityManagerInterface $entityManager): Response
    {
        // Empêcher la suppression de son propre compte
        if ($this->getUser() === $user) {
            $this->addFlash('error', 'Vous ne pouvez pas supprimer votre propre compte.');
            return $this->redirectToRoute('app_admin_user');
        }

        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé avec succès.');

        return $this->redirectToRoute('app_admin_user');
    }

    // #[Route('/make-me-admin/{email}', name: 'app_make_admin')]
    // public function makeAdmin(string $email, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    // {
    //     $user = $userRepository->findOneBy(['email' => $email]);

    //     if (!$user) {
    //         return new Response("Utilisateur non trouvé.", 404);
    //     }

    //     $user->setRoles(['ROLE_ADMIN']);
    //     $entityManager->flush();

    //     return new Response("L'utilisateur " . $email . " est maintenant ROLE_ADMIN ! Vous pouvez maintenant accéder à /admin.");
    // }
}
