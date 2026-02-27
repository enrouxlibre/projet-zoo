<?php

namespace App\Controller;

use App\Entity\Animals;
use App\Entity\Enclosure;
use App\Entity\Species;
use App\Repository\AnimalsRepository;
use App\Repository\EnclosureRepository;
use App\Repository\SpeciesRepository;
use Doctrine\ORM\EntityManagerInterface;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExtraAttributesException;
use Symfony\Component\Serializer\Exception\NotEncodableValueException;
use Symfony\Component\Serializer\Exception\NotNormalizableValueException;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class AnimalsController
{
    #[OA\Get(
        path: '/api/animals',
        summary: 'List all animals',
        tags: ['Animals'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Animals list',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                            new OA\Property(property: 'name', type: 'string', example: 'Blue'),
                            new OA\Property(property: 'gender', type: 'boolean', example: true),
                            new OA\Property(property: 'weight', type: 'integer', example: 2200),
                            new OA\Property(property: 'size', type: 'integer', example: 12),
                            new OA\Property(property: 'age', type: 'integer', example: 7),
                            new OA\Property(property: 'speciesId', type: 'integer', example: 1),
                            new OA\Property(property: 'enclosureId', type: 'integer', example: 1),
                            new OA\Property(
                                property: 'species',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Triceratops'),
                                    new OA\Property(property: 'diet', type: 'string', example: 'herbivorous'),
                                    new OA\Property(property: 'clearance', type: 'integer', example: 2),
                                ],
                                type: 'object',
                            ),
                            new OA\Property(
                                property: 'enclosure',
                                properties: [
                                    new OA\Property(property: 'id', type: 'integer', example: 1),
                                    new OA\Property(property: 'name', type: 'string', example: 'Plains Zone'),
                                    new OA\Property(property: 'clearance', type: 'integer', example: 2),
                                    new OA\Property(property: 'positionX', type: 'integer', example: 15),
                                    new OA\Property(property: 'positionY', type: 'integer', example: 8),
                                    new OA\Property(property: 'size', type: 'integer', example: 120),
                                ],
                                type: 'object',
                            ),
                        ],
                        type: 'object',
                    ),
                ),
            ),
        ],
    )]
    #[Route('/api/animals', name: 'animals_list', methods: ['GET'])]
    public function list(AnimalsRepository $animalsRepository, SerializerInterface $serializer): JsonResponse
    {
        $animalsList = $animalsRepository->findBy([], ['id' => 'ASC']);

        return $this->jsonResponse(
            array_map(
                fn(Animals $animal): array => $this->toResponse($animal),
                $animalsList,
            ),
            $serializer,
        );
    }

    #[OA\Get(
        path: '/api/animals/{id}',
        summary: 'Get one animal',
        tags: ['Animals'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Animal found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'uuid', type: 'string', format: 'uuid'),
                        new OA\Property(property: 'name', type: 'string', example: 'Blue'),
                        new OA\Property(property: 'gender', type: 'boolean', example: true),
                        new OA\Property(property: 'weight', type: 'integer', example: 2200),
                        new OA\Property(property: 'size', type: 'integer', example: 12),
                        new OA\Property(property: 'age', type: 'integer', example: 7),
                        new OA\Property(property: 'speciesId', type: 'integer', example: 1),
                        new OA\Property(property: 'enclosureId', type: 'integer', example: 1),
                        new OA\Property(
                            property: 'species',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Triceratops'),
                                new OA\Property(property: 'diet', type: 'string', example: 'herbivorous'),
                                new OA\Property(property: 'clearance', type: 'integer', example: 2),
                            ],
                            type: 'object',
                        ),
                        new OA\Property(
                            property: 'enclosure',
                            properties: [
                                new OA\Property(property: 'id', type: 'integer', example: 1),
                                new OA\Property(property: 'name', type: 'string', example: 'Plains Zone'),
                                new OA\Property(property: 'clearance', type: 'integer', example: 2),
                                new OA\Property(property: 'positionX', type: 'integer', example: 15),
                                new OA\Property(property: 'positionY', type: 'integer', example: 8),
                                new OA\Property(property: 'size', type: 'integer', example: 120),
                            ],
                            type: 'object',
                        ),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Animal not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Animal not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    #[Route('/api/animals/{id}', name: 'animals_get', methods: ['GET'])]
    public function getOne(?Animals $animals, SerializerInterface $serializer): JsonResponse
    {
        if ($animals === null) {
            return $this->jsonResponse([
                'error' => 'Animal not found.',
            ], $serializer, JsonResponse::HTTP_NOT_FOUND);
        }

        return $this->jsonResponse($this->toResponse($animals), $serializer);
    }

    #[OA\Post(
        path: '/api/animals',
        summary: 'Create an animal',
        tags: ['Animals'],
        parameters: [
            new OA\Parameter(
                name: 'X-CSRF-TOKEN',
                in: 'header',
                required: true,
                description: 'CSRF token for authentication',
                schema: new OA\Schema(type: 'string')
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'gender', 'weight', 'size', 'age', 'speciesId', 'enclosureId'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Blue'),
                    new OA\Property(property: 'gender', type: 'boolean', example: true),
                    new OA\Property(property: 'weight', type: 'integer', example: 2200),
                    new OA\Property(property: 'size', type: 'integer', example: 12),
                    new OA\Property(property: 'age', type: 'integer', example: 7),
                    new OA\Property(property: 'speciesId', type: 'integer', example: 1),
                    new OA\Property(property: 'enclosureId', type: 'integer', example: 1),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: 'Animal created'),
            new OA\Response(
                response: 400,
                description: 'Invalid JSON body or missing required fields',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string'),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Species or enclosure not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    #[Route('/api/animals', name: 'animals_create', methods: ['POST'])]
    public function create(
        Request $request,
        SpeciesRepository $speciesRepository,
        EnclosureRepository $enclosureRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
    ): JsonResponse {
        try {
            /** @var array<string, mixed> $payload */
            $payload = $serializer->deserialize(
                $request->getContent(),
                'array',
                'json',
                [
                    AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
                ],
            );
        } catch (NotEncodableValueException | NotNormalizableValueException | ExtraAttributesException) {
            return $this->jsonResponse([
                'error' => 'Invalid JSON body.',
            ], $serializer, JsonResponse::HTTP_BAD_REQUEST);
        }

        $animal = new Animals();
        $mappingResult = $this->mapPayloadToAnimals($animal, $payload, $speciesRepository, $enclosureRepository, $serializer);
        if ($mappingResult !== null) {
            return $mappingResult;
        }

        $entityManager->persist($animal);
        $entityManager->flush();

        return $this->jsonResponse([
            ...$this->toResponse($animal),
        ], $serializer, JsonResponse::HTTP_CREATED);
    }

    #[OA\Put(
        path: '/api/animals/{id}',
        summary: 'Update an animal',
        tags: ['Animals'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'gender', 'weight', 'size', 'age', 'speciesId', 'enclosureId'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Blue'),
                    new OA\Property(property: 'gender', type: 'boolean', example: true),
                    new OA\Property(property: 'weight', type: 'integer', example: 2200),
                    new OA\Property(property: 'size', type: 'integer', example: 12),
                    new OA\Property(property: 'age', type: 'integer', example: 7),
                    new OA\Property(property: 'speciesId', type: 'integer', example: 1),
                    new OA\Property(property: 'enclosureId', type: 'integer', example: 1),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: 'Animal updated'),
            new OA\Response(
                response: 400,
                description: 'Invalid JSON body or missing required fields',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string'),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Animal, species or enclosure not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    #[Route('/api/animals/{id}', name: 'animals_update', methods: ['PUT'])]
    public function update(
        ?Animals $animals,
        Request $request,
        SpeciesRepository $speciesRepository,
        EnclosureRepository $enclosureRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
    ): JsonResponse {
        if ($animals === null) {
            return $this->jsonResponse([
                'error' => 'Animal not found.',
            ], $serializer, JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            /** @var array<string, mixed> $payload */
            $payload = $serializer->deserialize(
                $request->getContent(),
                'array',
                'json',
                [
                    AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
                ],
            );
        } catch (NotEncodableValueException | NotNormalizableValueException | ExtraAttributesException) {
            return $this->jsonResponse([
                'error' => 'Invalid JSON body.',
            ], $serializer, JsonResponse::HTTP_BAD_REQUEST);
        }

        $mappingResult = $this->mapPayloadToAnimals($animals, $payload, $speciesRepository, $enclosureRepository, $serializer);
        if ($mappingResult !== null) {
            return $mappingResult;
        }

        $entityManager->flush();

        return $this->jsonResponse($this->toResponse($animals), $serializer);
    }

    #[OA\Delete(
        path: '/api/animals/{id}',
        summary: 'Delete an animal',
        tags: ['Animals'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Animal deleted'),
            new OA\Response(
                response: 404,
                description: 'Animal not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Animal not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    #[Route('/api/animals/{id}', name: 'animals_delete', methods: ['DELETE'])]
    public function delete(?Animals $animals, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($animals === null) {
            return new JsonResponse([
                'error' => 'Animal not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($animals);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param mixed $payload
     */
    private function mapPayloadToAnimals(
        Animals $animals,
        mixed $payload,
        SpeciesRepository $speciesRepository,
        EnclosureRepository $enclosureRepository,
        SerializerInterface $serializer,
    ): ?JsonResponse {
        if (!is_array($payload)) {
            return $this->jsonResponse([
                'error' => 'Invalid JSON body.',
            ], $serializer, JsonResponse::HTTP_BAD_REQUEST);
        }

        $allowedKeys = ['name', 'gender', 'weight', 'size', 'age', 'speciesId', 'enclosureId'];
        foreach (array_keys($payload) as $key) {
            if (!in_array($key, $allowedKeys, true)) {
                return $this->jsonResponse([
                    'error' => 'Invalid JSON body.',
                ], $serializer, JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        $name = $payload['name'] ?? null;
        $gender = $payload['gender'] ?? null;
        $weight = $payload['weight'] ?? null;
        $size = $payload['size'] ?? null;
        $age = $payload['age'] ?? null;
        $speciesId = $payload['speciesId'] ?? null;
        $enclosureId = $payload['enclosureId'] ?? null;

        if (
            !is_string($name)
            || trim($name) === ''
            || !is_bool($gender)
            || !is_int($weight)
            || !is_int($size)
            || !is_int($age)
            || !is_int($speciesId)
            || !is_int($enclosureId)
        ) {
            return $this->jsonResponse([
                'error' => 'Fields name, gender, weight, size, age, speciesId, and enclosureId are required.',
            ], $serializer, JsonResponse::HTTP_BAD_REQUEST);
        }

        $species = $speciesRepository->find($speciesId);
        if (!$species instanceof Species) {
            return $this->jsonResponse([
                'error' => 'Species not found.',
            ], $serializer, JsonResponse::HTTP_NOT_FOUND);
        }

        $enclosure = $enclosureRepository->find($enclosureId);
        if (!$enclosure instanceof Enclosure) {
            return $this->jsonResponse([
                'error' => 'Enclosure not found.',
            ], $serializer, JsonResponse::HTTP_NOT_FOUND);
        }

        $speciesClearance = $species->getClearance();
        $enclosureClearance = $enclosure->getClearance();

        if ($speciesClearance === null || $enclosureClearance === null || $enclosureClearance->value < $speciesClearance->value) {
            return $this->jsonResponse([
                'error' => 'Enclosure clearance is too low for this animal.',
            ], $serializer, JsonResponse::HTTP_BAD_REQUEST);
        }

        $animals
            ->setName(trim($name))
            ->setGender($gender)
            ->setWeight($weight)
            ->setSize($size)
            ->setAge($age)
            ->setSpecies($species)
            ->setEnclosure($enclosure);

        return null;
    }

    private function toResponse(Animals $animals): array
    {
        $species = $animals->getSpecies();
        $enclosure = $animals->getEnclosure();

        return [
            'id' => $animals->getId(),
            'uuid' => $animals->getUuid()?->toRfc4122(),
            'name' => $animals->getName(),
            'gender' => $animals->isGender(),
            'weight' => $animals->getWeight(),
            'size' => $animals->getSize(),
            'age' => $animals->getAge(),
            'speciesId' => $species?->getId(),
            'enclosureId' => $enclosure?->getId(),
            'species' => $species === null ? null : [
                'id' => $species->getId(),
                'name' => $species->getName(),
                'diet' => $species->getDiet()?->value,
                'clearance' => $species->getClearance()?->value,
            ],
            'enclosure' => $enclosure === null ? null : [
                'id' => $enclosure->getId(),
                'name' => $enclosure->getName(),
                'clearance' => $enclosure->getClearance()?->value,
                'positionX' => $enclosure->getPositionX(),
                'positionY' => $enclosure->getPositionY(),
                'size' => $enclosure->getSize(),
            ],
        ];
    }

    private function jsonResponse(mixed $payload, SerializerInterface $serializer, int $status = JsonResponse::HTTP_OK): JsonResponse
    {
        return JsonResponse::fromJsonString(
            $serializer->serialize($payload, 'json'),
            $status,
        );
    }
}
