<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

final class AdminUserController extends AbstractController
{
    #[Route('/user', name: 'app_admin_user')]
    public function index(UserRepository $userRepo): Response
    {
        $users = $userRepo->findAll();
        return $this->render('Admin/user/index.html.twig', [
            'title' => 'Liste des utilisateurs',
            'user' => $users,
        ]);
    }
    #[Route('/user/{id}/edit', name: 'app_admin_user_edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $em->flush();
            $this->addFlash('success', 'L\'utilisateur a correctement été modifié');
            return $this->redirectToRoute('app_admin_user');
        }
        return $this->render('Admin/user/edit.html.twig', [
            'title' => 'Edition de l\'utilisateur',
            'user' => $user,
            'form' => $form
        ]);
    }

    #[Route('/user/{id}/delete', name: 'app_admin_user_delete')]
    public function delete(User $user, EntityManagerInterface $em): Response
    {
        $em->remove($user);
        $em->flush();
        $this->addFlash('success', 'L\'utilisateur a correctement été supprimé');
        return $this->redirectToRoute('app_admin_user');
    }
}
