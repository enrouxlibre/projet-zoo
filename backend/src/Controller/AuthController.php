<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class AuthController
{
    public function __construct(
        private readonly string $jwtCookieName,
        private readonly string $jwtCookiePath,
    ) {}
    #[Route('/api/login', name: 'app_login', methods: ['POST'])]
    public function login(): Response
    {
        // Handled by the json_login authenticator and LoginSuccessEventSubscriber
        return new Response('', Response::HTTP_NO_CONTENT);
    }

    #[Route('/api/logout', name: 'app_logout', methods: ['POST'])]
    public function logout(): JsonResponse
    {
        $response = new JsonResponse([
            "message" => "Logged out successfully.",
        ], Response::HTTP_OK);
        $response->headers->clearCookie($this->jwtCookieName, path: $this->jwtCookiePath);
        // Handled by the logout firewall and LogoutEventSubscriber
        return $response;
    }
}
