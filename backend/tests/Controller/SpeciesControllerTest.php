<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Enum\ClearanceLevel;
use App\Enum\SpeciesDiet;
use Symfony\Component\BrowserKit\Cookie;

final class SpeciesControllerTest extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = true;

    public function testCreateSpeciesEndpoint(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $name = 'Test Dinosaur ' . uniqid('', true);

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'POST',
            uri: '/api/species',
            options: [
                'json' => [
                    'name' => $name,
                    'diet' => SpeciesDiet::CARNIVOROUS->value,
                    'clearance' => ClearanceLevel::HIGH->value,
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
        self::assertSame(SpeciesDiet::CARNIVOROUS->value, $created['diet']);
        self::assertSame(ClearanceLevel::HIGH->value, $created['clearance']);
    }

    public function testGetSpeciesEndpoint(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $speciesId = $this->createSpecies(
            $client,
            $csrfToken,
            'Get Endpoint Dino ' . uniqid('', true),
            SpeciesDiet::HERBIVOROUS->value,
            ClearanceLevel::LOW->value,
        );

        $client->request('GET', '/api/species/' . $speciesId);
        self::assertResponseIsSuccessful();
        $fetched = $this->decodeJsonResponse($client);
        self::assertSame($speciesId, $fetched['id']);
    }

    public function testListSpeciesEndpoint(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $createdName = 'List Endpoint Dino ' . uniqid('', true);
        $speciesId = $this->createSpecies(
            $client,
            $csrfToken,
            $createdName,
            SpeciesDiet::OMNIVOROUS->value,
            ClearanceLevel::LOW->value,
        );

        $client->request('GET', '/api/species');
        self::assertResponseIsSuccessful();

        $list = $this->decodeJsonResponse($client);
        self::assertIsArray($list);

        $matching = array_values(array_filter(
            $list,
            static fn(array $item): bool => (int) ($item['id'] ?? 0) === $speciesId,
        ));

        self::assertCount(1, $matching);
        self::assertSame($createdName, $matching[0]['name']);
        self::assertSame(SpeciesDiet::OMNIVOROUS->value, $matching[0]['diet']);
        self::assertSame(ClearanceLevel::LOW->value, $matching[0]['clearance']);
    }

    public function testUpdateSpeciesEndpoint(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $speciesId = $this->createSpecies(
            $client,
            $csrfToken,
            'Update Endpoint Dino ' . uniqid('', true),
            SpeciesDiet::CARNIVOROUS->value,
            ClearanceLevel::HIGH->value,
        );

        $updatedName = 'Updated Endpoint Dino ' . uniqid('', true);

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'PUT',
            uri: '/api/species/' . $speciesId,
            options: [
                'json' => [
                    'name' => $updatedName,
                    'diet' => SpeciesDiet::OMNIVOROUS->value,
                    'clearance' => ClearanceLevel::MODERATE->value,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        );

        self::assertResponseIsSuccessful();
        $updated = $this->decodeJsonResponse($client);
        self::assertSame($updatedName, $updated['name']);
        self::assertSame(SpeciesDiet::OMNIVOROUS->value, $updated['diet']);
        self::assertSame(ClearanceLevel::MODERATE->value, $updated['clearance']);
    }

    public function testDeleteSpeciesEndpoint(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $speciesId = $this->createSpecies(
            $client,
            $csrfToken,
            'Delete Endpoint Dino ' . uniqid('', true),
            SpeciesDiet::CARNIVOROUS->value,
            ClearanceLevel::MODERATE->value,
        );

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'DELETE',
            uri: '/api/species/' . $speciesId,
        );

        self::assertResponseStatusCodeSame(204);

        $client->request('GET', '/api/species/' . $speciesId);
        self::assertResponseStatusCodeSame(404);
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
        self::assertArrayHasKey('id', $created);

        return (int) $created['id'];
    }

    /**
     * @return array{0: Client, 1: string}
     */
    private function createAuthenticatedClient(): array
    {
        $client = static::createClient();

        $email = 'test@example.com';
        $plainPassword = 'test1234';

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
                    'email' => $email,
                    'password' => $plainPassword,
                ],
            ],
        );

        self::assertResponseIsSuccessful();

        $loginPayload = $this->decodeJsonResponse($client);
        self::assertArrayHasKey('csrfToken', $loginPayload);

        $csrfToken = (string) $loginPayload['csrfToken'];
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

        $client->request(
            $method,
            $uri,
            $options,
        );
    }

    private function setCsrfCookie(Client $client, string $csrfToken): void
    {
        $client->getCookieJar()->set(
            new Cookie(
                'X-CSRF-TOKEN',
                $csrfToken,
                null,
                '/',
                'localhost',
            ),
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
