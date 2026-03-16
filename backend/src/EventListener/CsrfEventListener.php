<?php

namespace App\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpFoundation\Response;
use Psr\Log\LoggerInterface;

class CsrfEventListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

    public function onKernelRequest(RequestEvent $event)
    {
        $request = $event->getRequest();

        if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            $this->logger->debug('CSRF check skipped.', [
                'method' => $request->getMethod(),
                'path' => $request->getPathInfo(),
            ]);
            return;
        }

        $cookieToken = $request->cookies->get('X-CSRF-TOKEN');
        $headerToken = $request->headers->get('X-CSRF-TOKEN');

        if (!$cookieToken || !$headerToken || !hash_equals($cookieToken, $headerToken)) {
            $this->logger->warning('CSRF token mismatch.', [
                'method' => $request->getMethod(),
                'path' => $request->getPathInfo(),
                'has_cookie' => (bool) $cookieToken,
                'has_header' => (bool) $headerToken,
            ]);
            $event->setResponse(new Response('Invalid CSRF token', 403));
            return;
        }

        $this->logger->info('CSRF token validated.', [
            'method' => $request->getMethod(),
            'path' => $request->getPathInfo(),
        ]);
    }
}