<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Enum\ClearanceLevel;
use App\Enum\SpeciesDiet;
use Symfony\Component\BrowserKit\Cookie;

final class EnclosureControllerTest extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = true;

    public function testCreateEnclosureEndpoint(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $name = 'Test Enclosure ' . uniqid('', true);

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'POST',
            uri: '/api/enclosures',
            options: [
                'json' => [
                    'name' => $name,
                    'clearance' => ClearanceLevel::HIGH->value,
                    'positionX' => 10,
                    'positionY' => 20,
                    'size' => 150,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(201);
        $created = $this->decodeJsonResponse($client);
        self::assertArrayHasKey('id', $created);
        self::assertSame($name, $created['name']);
        self::assertSame(ClearanceLevel::HIGH->value, $created['clearance']);
    }

    public function testGetEnclosureEndpoint(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $enclosureId = $this->createEnclosure(
            $client,
            $csrfToken,
            'Get Enclosure ' . uniqid('', true),
            ClearanceLevel::LOW->value,
            5,
            7,
            100,
        );

        $client->request('GET', '/api/enclosures/' . $enclosureId);
        self::assertResponseIsSuccessful();

        $fetched = $this->decodeJsonResponse($client);
        self::assertSame($enclosureId, $fetched['id']);
    }

    public function testListEnclosureEndpoint(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $name = 'List Enclosure ' . uniqid('', true);
        $enclosureId = $this->createEnclosure(
            $client,
            $csrfToken,
            $name,
            ClearanceLevel::MODERATE->value,
            15,
            25,
            120,
        );

        $client->request('GET', '/api/enclosures');
        self::assertResponseIsSuccessful();

        $list = $this->decodeJsonResponse($client);
        self::assertIsArray($list);

        $matching = array_values(array_filter(
            $list,
            static fn(array $item): bool => (int) ($item['id'] ?? 0) === $enclosureId,
        ));

        self::assertCount(1, $matching);
        self::assertSame($name, $matching[0]['name']);
    }

    public function testUpdateEnclosureEndpoint(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $enclosureId = $this->createEnclosure(
            $client,
            $csrfToken,
            'Update Enclosure ' . uniqid('', true),
            ClearanceLevel::HIGH->value,
            12,
            18,
            110,
        );

        $updatedName = 'Updated Enclosure ' . uniqid('', true);

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'PUT',
            uri: '/api/enclosures/' . $enclosureId,
            options: [
                'json' => [
                    'name' => $updatedName,
                    'clearance' => ClearanceLevel::MODERATE->value,
                    'positionX' => 30,
                    'positionY' => 40,
                    'size' => 160,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        );

        self::assertResponseIsSuccessful();
        $updated = $this->decodeJsonResponse($client);
        self::assertSame($updatedName, $updated['name']);
        self::assertSame(ClearanceLevel::MODERATE->value, $updated['clearance']);
        self::assertSame(30, $updated['positionX']);
        self::assertSame(40, $updated['positionY']);
        self::assertSame(160, $updated['size']);
    }

    public function testDeleteEnclosureEndpoint(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $enclosureId = $this->createEnclosure(
            $client,
            $csrfToken,
            'Delete Enclosure ' . uniqid('', true),
            ClearanceLevel::LOW->value,
            3,
            6,
            90,
        );

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'DELETE',
            uri: '/api/enclosures/' . $enclosureId,
        );

        self::assertResponseStatusCodeSame(204);

        $client->request('GET', '/api/enclosures/' . $enclosureId);
        self::assertResponseStatusCodeSame(404);
    }

    public function testUpdateEnclosureFailsWhenClearanceTooLowForAssignedAnimals(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $speciesId = $this->createSpecies(
            $client,
            $csrfToken,
            'Dangerous Species ' . uniqid('', true),
            SpeciesDiet::CARNIVOROUS->value,
            ClearanceLevel::HIGH->value,
        );

        $enclosureId = $this->createEnclosure(
            $client,
            $csrfToken,
            'Protected Enclosure ' . uniqid('', true),
            ClearanceLevel::HIGH->value,
            20,
            20,
            140,
        );

        $this->createAnimal(
            $client,
            $csrfToken,
            'Guarded Animal ' . uniqid('', true),
            true,
            2000,
            8,
            6,
            $speciesId,
            $enclosureId,
        );

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'PUT',
            uri: '/api/enclosures/' . $enclosureId,
            options: [
                'json' => [
                    'name' => 'Lowered Enclosure',
                    'clearance' => ClearanceLevel::LOW->value,
                    'positionX' => 20,
                    'positionY' => 20,
                    'size' => 140,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(400);
        $payload = $this->decodeJsonResponse($client);
        self::assertSame('Enclosure clearance is too low for assigned animals.', $payload['error'] ?? null);
    }

    private function createEnclosure(
        Client $client,
        string $csrfToken,
        string $name,
        int $clearance,
        int $positionX,
        int $positionY,
        int $size,
    ): int {
        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'POST',
            uri: '/api/enclosures',
            options: [
                'json' => [
                    'name' => $name,
                    'clearance' => $clearance,
                    'positionX' => $positionX,
                    'positionY' => $positionY,
                    'size' => $size,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(201);
        $created = $this->decodeJsonResponse($client);

        return (int) ($created['id'] ?? 0);
    }

    private function createSpecies(
        Client $client,
        string $csrfToken,
        string $name,
        string $diet,
        int $clearance,
    ): int {
        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'POST',
            uri: '/api/species',
            options: [
                'json' => [
                    'name' => $name,
                    'diet' => $diet,
                    'clearance' => $clearance,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(201);
        $created = $this->decodeJsonResponse($client);

        return (int) ($created['id'] ?? 0);
    }

    private function createAnimal(
        Client $client,
        string $csrfToken,
        string $name,
        bool $gender,
        int $weight,
        int $size,
        int $age,
        int $speciesId,
        int $enclosureId,
    ): int {
        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'POST',
            uri: '/api/animals',
            options: [
                'json' => [
                    'name' => $name,
                    'gender' => $gender,
                    'weight' => $weight,
                    'size' => $size,
                    'age' => $age,
                    'speciesId' => $speciesId,
                    'enclosureId' => $enclosureId,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(201);
        $created = $this->decodeJsonResponse($client);

        return (int) ($created['id'] ?? 0);
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
