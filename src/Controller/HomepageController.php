<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomepageController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function index(): Response
    {
        return $this->render('homepage/index.html.twig', [
            'controller_name' => 'HomepageController',
        ]);
    }

    #[Route('/admin', name: 'admin_homepage')]
    public function admin(): Response
    {
        return $this->render('homepage/admin.html.twig', []);
    }

    #[Route('/customer', name: 'customer_homepage')]
    public function customer(): Response
    {
        return $this->render('homepage/customer.html.twig', []);
    }
}
