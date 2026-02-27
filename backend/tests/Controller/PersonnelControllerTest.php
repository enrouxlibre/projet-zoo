<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Enum\ClearanceLevel;
use Symfony\Component\BrowserKit\Cookie;

final class PersonnelControllerTest extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = true;

    public function testListPersonnelEndpoint(): void
    {
        [$client] = $this->createAuthenticatedClient();

        $client->request('GET', '/api/personnel');

        self::assertResponseIsSuccessful();
        $payload = $this->decodeJsonResponse($client);
        self::assertIsArray($payload);
        self::assertNotEmpty($payload);

        $first = $payload[0] ?? null;
        self::assertIsArray($first);
        self::assertArrayHasKey('userId', $first);
        self::assertArrayHasKey('email', $first);
        self::assertArrayHasKey('firstName', $first);
        self::assertArrayHasKey('lastName', $first);
        self::assertArrayHasKey('job', $first);
        self::assertArrayHasKey('clearance', $first);
        self::assertArrayHasKey('dateOfBirth', $first);
    }

    public function testGetPersonnelEndpoint(): void
    {
        [$client] = $this->createAuthenticatedClient();

        $client->request('GET', '/api/personnel');
        self::assertResponseIsSuccessful();
        $list = $this->decodeJsonResponse($client);

        $firstUserId = (int) ($list[0]['userId'] ?? 0);
        self::assertGreaterThan(0, $firstUserId);

        $client->request('GET', '/api/personnel/' . $firstUserId);
        self::assertResponseIsSuccessful();
        $item = $this->decodeJsonResponse($client);

        self::assertSame($firstUserId, $item['userId'] ?? null);
    }

    public function testGetPersonnelEndpointNotFound(): void
    {
        [$client] = $this->createAuthenticatedClient();

        $client->request('GET', '/api/personnel/999999');

        self::assertResponseStatusCodeSame(404);
        $payload = $this->decodeJsonResponse($client);
        self::assertSame('Personnel entry not found.', $payload['error'] ?? null);
    }

    public function testUpdatePersonnelEndpoint(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $client->request('GET', '/api/personnel');
        self::assertResponseIsSuccessful();
        $list = $this->decodeJsonResponse($client);

        $firstUserId = (int) ($list[0]['userId'] ?? 0);
        self::assertGreaterThan(0, $firstUserId);

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'PUT',
            uri: '/api/personnel/' . $firstUserId,
            options: [
                'json' => [
                    'firstName' => 'UpdatedFirst',
                    'lastName' => 'UpdatedLast',
                    'telephone' => '+33123450000',
                    'job' => 'Updated Job',
                    'clearance' => ClearanceLevel::HIGH->value,
                    'dateOfBirth' => '1991-08-17',
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        );

        self::assertResponseIsSuccessful();
        $updated = $this->decodeJsonResponse($client);

        self::assertSame($firstUserId, $updated['userId'] ?? null);
        self::assertSame('UpdatedFirst', $updated['firstName'] ?? null);
        self::assertSame('UpdatedLast', $updated['lastName'] ?? null);
        self::assertSame('+33123450000', $updated['telephone'] ?? null);
        self::assertSame('Updated Job', $updated['job'] ?? null);
        self::assertSame(ClearanceLevel::HIGH->value, $updated['clearance'] ?? null);
        self::assertSame('1991-08-17', $updated['dateOfBirth'] ?? null);
    }

    public function testUpdatePersonnelEndpointInvalidClearance(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $client->request('GET', '/api/personnel');
        self::assertResponseIsSuccessful();
        $list = $this->decodeJsonResponse($client);

        $firstUserId = (int) ($list[0]['userId'] ?? 0);
        self::assertGreaterThan(0, $firstUserId);

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'PUT',
            uri: '/api/personnel/' . $firstUserId,
            options: [
                'json' => [
                    'firstName' => 'Invalid',
                    'lastName' => 'Clearance',
                    'telephone' => '+33111111111',
                    'job' => 'Any Job',
                    'clearance' => 99,
                    'dateOfBirth' => '1990-01-01',
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(400);
        $payload = $this->decodeJsonResponse($client);
        self::assertSame('Invalid clearance value.', $payload['error'] ?? null);
    }

    public function testUpdatePersonnelEndpointInvalidDateOfBirth(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $client->request('GET', '/api/personnel');
        self::assertResponseIsSuccessful();
        $list = $this->decodeJsonResponse($client);

        $firstUserId = (int) ($list[0]['userId'] ?? 0);
        self::assertGreaterThan(0, $firstUserId);

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'PUT',
            uri: '/api/personnel/' . $firstUserId,
            options: [
                'json' => [
                    'firstName' => 'Invalid',
                    'lastName' => 'Date',
                    'telephone' => '+33111111112',
                    'job' => 'Any Job',
                    'clearance' => ClearanceLevel::LOW->value,
                    'dateOfBirth' => 'not-a-date',
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(400);
        $payload = $this->decodeJsonResponse($client);
        self::assertSame('Invalid dateOfBirth value. Expected a valid date.', $payload['error'] ?? null);
    }

    /**
     * @return array{0: Client, 1: string}
     */
    private function createAuthenticatedClient(): array
    {
        $client = static::createClient();

        $bootstrapCsrf = 'bootstrap-csrf-token';
        $client->getCookieJar()->set(new Cookie('X-CSRF-TOKEN', $bootstrapCsrf));

        $client->request(
            'POST',
            '/api/login',
            [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'X-CSRF-TOKEN' => $bootstrapCsrf,
                ],
                'json' => [
                    'email' => 'test@example.com',
                    'password' => 'test1234',
                ],
            ],
        );

        self::assertResponseIsSuccessful();

        $loginPayload = $this->decodeJsonResponse($client);
        $csrfToken = (string) ($loginPayload['csrfToken'] ?? '');
        self::assertNotSame('', $csrfToken);

        $this->setCsrfCookie($client, $csrfToken);

        return [$client, $csrfToken];
    }

    /**
     * @param array<string, mixed> $options
     */
    private function requestWithCsrf(
        Client $client,
        string $csrfToken,
        string $method,
        string $uri,
        array $options = [],
    ): void {
        $this->setCsrfCookie($client, $csrfToken);

        $options['headers'] ??= [];
        $options['headers']['X-CSRF-TOKEN'] = $csrfToken;

        $client->request($method, $uri, $options);
    }

    private function setCsrfCookie(Client $client, string $csrfToken): void
    {
        $client->getCookieJar()->set(
            new Cookie('X-CSRF-TOKEN', $csrfToken, null, '/', 'localhost'),
        );
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeJsonResponse(Client $client): array
    {
        $response = $client->getResponse();
        self::assertNotNull($response);

        return $response->toArray(false);
    }
}
