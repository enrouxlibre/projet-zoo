<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthController
{
    #[Route('/api/login', name: 'app_login', methods: ['POST'])]
    public function login(): Response
    {
        // Handled by the json_login authenticator and LoginSuccessEventSubscriber
        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): Response
    {
        // Handled by the logout firewall and LogoutEventSubscriber
        return new Response('', Response::HTTP_NO_CONTENT);
    }
}
