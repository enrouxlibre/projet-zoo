<?php

namespace App\Security;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Http\Event\LogoutEvent;

class LogoutEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private readonly string $jwtCookieName,
        private readonly string $jwtCookiePath,
    ) {}

    public static function getSubscribedEvents(): array
    {
        return [
            LogoutEvent::class => 'onLogout',
        ];
    }

    public function onLogout(LogoutEvent $event): void
    {
        $response = new JsonResponse(['message' => 'Logged out successfully.']);
        $response->headers->clearCookie($this->jwtCookieName, $this->jwtCookiePath);

        $event->setResponse($response);
    }
}
