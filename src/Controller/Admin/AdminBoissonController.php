<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminBoissonController extends AbstractController
{
    #[Route('/admin/boisson', name: 'app_admin_boisson')]
    public function index(): Response
    {
        return $this->render('admin/boisson/index.html.twig', [
            'controller_name' => 'AdminBoissonController',
        ]);
    }
}
