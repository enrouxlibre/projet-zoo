<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly string $jwtCookieName,
        private readonly string $jwtCookiePath,
        private readonly bool $jwtCookieSecure,
        private readonly string $jwtCookieSameSite,
    ) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): JsonResponse
    {
        /** @var UserInterface $user */
        $user = $token->getUser();
        $token = $this->jwtManager->create($user);

        $response = new JsonResponse(['message' => 'Login successful.']);
        $cookie = Cookie::create(
            name: $this->jwtCookieName,
            value: $token,
            path: $this->jwtCookiePath,
            secure: $this->jwtCookieSecure,
            httpOnly: true,
            sameSite: $this->jwtCookieSameSite,
        );

        $response->headers->setCookie($cookie);

        return $response;
    }
}
