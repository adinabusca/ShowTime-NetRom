<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class LoginController extends AbstractController
{
    #[Route('/login', name: 'app_login')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastEmail = $authenticationUtils->getLastUsername();
//        $lastPassword = $authenticationUtils->getLastUsername();

        return $this->render('login/index.html.twig', [

            'last_email' => $lastEmail,
//            'last_password' => $lastPassword,
            'error' => $error,
        ]);
    }
}
