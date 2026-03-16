<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use App\Enum\ClearanceLevel;
use App\Enum\SpeciesDiet;
use Symfony\Component\BrowserKit\Cookie;

final class AnimalsControllerTest extends ApiTestCase
{
    protected static ?bool $alwaysBootKernel = true;

    public function testCreateAnimalEndpointWithNestedSpeciesAndEnclosure(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $speciesId = $this->createSpecies(
            $client,
            $csrfToken,
            'Animal Species ' . uniqid('', true),
            SpeciesDiet::OMNIVOROUS->value,
            ClearanceLevel::MODERATE->value,
        );

        $enclosureId = $this->createEnclosure(
            $client,
            $csrfToken,
            'Animal Enclosure ' . uniqid('', true),
            ClearanceLevel::HIGH->value,
            18,
            22,
            130,
        );

        $animalName = 'Animal ' . uniqid('', true);

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'POST',
            uri: '/api/animals',
            options: [
                'json' => [
                    'name' => $animalName,
                    'gender' => true,
                    'weight' => 1900,
                    'size' => 7,
                    'age' => 4,
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
        self::assertSame($animalName, $created['name'] ?? null);
        self::assertSame($speciesId, $created['speciesId'] ?? null);
        self::assertSame($enclosureId, $created['enclosureId'] ?? null);

        self::assertIsArray($created['species'] ?? null);
        self::assertSame($speciesId, $created['species']['id'] ?? null);

        self::assertIsArray($created['enclosure'] ?? null);
        self::assertSame($enclosureId, $created['enclosure']['id'] ?? null);
    }

    public function testGetAndListAnimalEndpoints(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $speciesId = $this->createSpecies(
            $client,
            $csrfToken,
            'Get Species ' . uniqid('', true),
            SpeciesDiet::HERBIVOROUS->value,
            ClearanceLevel::LOW->value,
        );

        $enclosureId = $this->createEnclosure(
            $client,
            $csrfToken,
            'Get Enclosure ' . uniqid('', true),
            ClearanceLevel::LOW->value,
            6,
            9,
            100,
        );

        $animalName = 'Get Animal ' . uniqid('', true);
        $animalId = $this->createAnimal(
            $client,
            $csrfToken,
            $animalName,
            false,
            1200,
            5,
            3,
            $speciesId,
            $enclosureId,
        );

        $client->request('GET', '/api/animals/' . $animalId);
        self::assertResponseIsSuccessful();

        $fetched = $this->decodeJsonResponse($client);
        self::assertSame($animalId, $fetched['id'] ?? null);
        self::assertSame($speciesId, $fetched['species']['id'] ?? null);
        self::assertSame($enclosureId, $fetched['enclosure']['id'] ?? null);

        $client->request('GET', '/api/animals');
        self::assertResponseIsSuccessful();

        $list = $this->decodeJsonResponse($client);
        self::assertIsArray($list);

        $matching = array_values(array_filter(
            $list,
            static fn(array $item): bool => (int) ($item['id'] ?? 0) === $animalId,
        ));

        self::assertCount(1, $matching);
        self::assertSame($animalName, $matching[0]['name'] ?? null);
    }

    public function testUpdateAnimalEndpoint(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $speciesId = $this->createSpecies(
            $client,
            $csrfToken,
            'Update Species ' . uniqid('', true),
            SpeciesDiet::CARNIVOROUS->value,
            ClearanceLevel::HIGH->value,
        );

        $highEnclosureId = $this->createEnclosure(
            $client,
            $csrfToken,
            'High Enclosure ' . uniqid('', true),
            ClearanceLevel::CRITICAL->value,
            12,
            14,
            160,
        );

        $animalId = $this->createAnimal(
            $client,
            $csrfToken,
            'Update Animal ' . uniqid('', true),
            true,
            2600,
            9,
            5,
            $speciesId,
            $highEnclosureId,
        );

        $updatedName = 'Updated Animal ' . uniqid('', true);

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'PUT',
            uri: '/api/animals/' . $animalId,
            options: [
                'json' => [
                    'name' => $updatedName,
                    'gender' => false,
                    'weight' => 2550,
                    'size' => 10,
                    'age' => 6,
                    'speciesId' => $speciesId,
                    'enclosureId' => $highEnclosureId,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        );

        self::assertResponseIsSuccessful();
        $updated = $this->decodeJsonResponse($client);
        self::assertSame($updatedName, $updated['name'] ?? null);
        self::assertSame(false, $updated['gender'] ?? null);
    }

    public function testDeleteAnimalEndpoint(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $speciesId = $this->createSpecies(
            $client,
            $csrfToken,
            'Delete Species ' . uniqid('', true),
            SpeciesDiet::HERBIVOROUS->value,
            ClearanceLevel::LOW->value,
        );

        $enclosureId = $this->createEnclosure(
            $client,
            $csrfToken,
            'Delete Enclosure ' . uniqid('', true),
            ClearanceLevel::LOW->value,
            4,
            4,
            90,
        );

        $animalId = $this->createAnimal(
            $client,
            $csrfToken,
            'Delete Animal ' . uniqid('', true),
            true,
            900,
            4,
            2,
            $speciesId,
            $enclosureId,
        );

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'DELETE',
            uri: '/api/animals/' . $animalId,
        );

        self::assertResponseStatusCodeSame(204);

        $client->request('GET', '/api/animals/' . $animalId);
        self::assertResponseStatusCodeSame(404);
    }

    public function testCreateAnimalFailsWhenEnclosureClearanceTooLow(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $speciesId = $this->createSpecies(
            $client,
            $csrfToken,
            'Restricted Species ' . uniqid('', true),
            SpeciesDiet::CARNIVOROUS->value,
            ClearanceLevel::HIGH->value,
        );

        $lowEnclosureId = $this->createEnclosure(
            $client,
            $csrfToken,
            'Low Enclosure ' . uniqid('', true),
            ClearanceLevel::LOW->value,
            11,
            13,
            100,
        );

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'POST',
            uri: '/api/animals',
            options: [
                'json' => [
                    'name' => 'Blocked Animal ' . uniqid('', true),
                    'gender' => true,
                    'weight' => 2100,
                    'size' => 8,
                    'age' => 5,
                    'speciesId' => $speciesId,
                    'enclosureId' => $lowEnclosureId,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(400);
        $payload = $this->decodeJsonResponse($client);
        self::assertSame('Enclosure clearance is too low for this animal.', $payload['error'] ?? null);
    }

    public function testUpdateAnimalFailsWhenEnclosureClearanceTooLow(): void
    {
        [$client, $csrfToken] = $this->createAuthenticatedClient();

        $speciesId = $this->createSpecies(
            $client,
            $csrfToken,
            'Update Restricted Species ' . uniqid('', true),
            SpeciesDiet::CARNIVOROUS->value,
            ClearanceLevel::HIGH->value,
        );

        $highEnclosureId = $this->createEnclosure(
            $client,
            $csrfToken,
            'Update High Enclosure ' . uniqid('', true),
            ClearanceLevel::HIGH->value,
            14,
            16,
            150,
        );

        $lowEnclosureId = $this->createEnclosure(
            $client,
            $csrfToken,
            'Update Low Enclosure ' . uniqid('', true),
            ClearanceLevel::LOW->value,
            21,
            22,
            100,
        );

        $animalId = $this->createAnimal(
            $client,
            $csrfToken,
            'Movable Animal ' . uniqid('', true),
            true,
            2300,
            9,
            6,
            $speciesId,
            $highEnclosureId,
        );

        $this->requestWithCsrf(
            $client,
            $csrfToken,
            method: 'PUT',
            uri: '/api/animals/' . $animalId,
            options: [
                'json' => [
                    'name' => 'Move Attempt',
                    'gender' => true,
                    'weight' => 2300,
                    'size' => 9,
                    'age' => 6,
                    'speciesId' => $speciesId,
                    'enclosureId' => $lowEnclosureId,
                ],
                'headers' => [
                    'Content-Type' => 'application/json',
                ],
            ],
        );

        self::assertResponseStatusCodeSame(400);
        $payload = $this->decodeJsonResponse($client);
        self::assertSame('Enclosure clearance is too low for this animal.', $payload['error'] ?? null);
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
