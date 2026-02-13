<?php

namespace App\Security;

use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTTokenManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\LoginSuccessEvent;

class LoginSuccessEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly JWTTokenManagerInterface $jwtManager,
        private readonly string $jwtCookieName,
        private readonly bool $jwtCookieSecure,
        private readonly string $jwtCookieSameSite,
        private readonly string $jwtCookiePath,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            LoginSuccessEvent::class => 'onLoginSuccess',
        ];
    }

    public function onLoginSuccess(LoginSuccessEvent $event): void
    {
        $user = $event->getUser();
        if (!$user instanceof UserInterface) {
            return;
        }

        $jwt = $this->jwtManager->create($user);

        $response = new JsonResponse([
            'user' => [
                'email' => $user->getUserIdentifier(),
                'roles' => $user->getRoles(),
            ],
        ]);

        $cookie = Cookie::create($this->jwtCookieName)
            ->withValue($jwt)
            ->withPath($this->jwtCookiePath)
            ->withSecure($this->jwtCookieSecure)
            ->withHttpOnly(true)
            ->withSameSite($this->jwtCookieSameSite);

        $response->headers->setCookie($cookie);

        $event->setResponse($response);
    }
}
