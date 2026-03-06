<?php

namespace App\Controller;

use App\Entity\Enclosure;
use App\Repository\AnimalsRepository;
use App\Repository\EnclosureRepository;
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

class EnclosureController
{
    #[OA\Get(
        path: '/api/enclosures',
        summary: 'List all enclosures',
        tags: ['Enclosure'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Enclosure list',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Carnivore Habitat A'),
                            new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                            new OA\Property(property: 'positionX', type: 'integer', example: 10),
                            new OA\Property(property: 'positionY', type: 'integer', example: 20),
                            new OA\Property(property: 'size', type: 'integer', example: 100),
                        ],
                        type: 'object',
                    ),
                ),
            ),
        ],
    )]
    #[Route('/api/enclosures', name: 'enclosure_list', methods: ['GET'])]
    public function list(EnclosureRepository $enclosureRepository, SerializerInterface $serializer): JsonResponse
    {
        $enclosureList = $enclosureRepository->findBy([], ['id' => 'ASC']);
        $response = $serializer->serialize($enclosureList, 'json', [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'name',
                'clearance',
                'positionX',
                'positionY',
                'size',
                'animals' => ['id', 'uuid', 'name', 'gender', 'weight', 'size', 'age', 'species' => ['id', 'name', 'clearance', 'diet']],
            ],
        ]);

        return new JsonResponse($response, JsonResponse::HTTP_OK, [], true);
    }

    #[OA\Get(
        path: '/api/enclosures/{id}',
        summary: 'Get one enclosure',
        tags: ['Enclosure'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Enclosure found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Carnivore Habitat A'),
                        new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                        new OA\Property(property: 'positionX', type: 'integer', example: 10),
                        new OA\Property(property: 'positionY', type: 'integer', example: 20),
                        new OA\Property(property: 'size', type: 'integer', example: 100),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Enclosure not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Enclosure not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    #[Route('/api/enclosures/{id}', name: 'enclosure_get', methods: ['GET'])]
    public function getOne(?Enclosure $enclosure): JsonResponse
    {
        if ($enclosure === null) {
            return new JsonResponse([
                'error' => 'Enclosure not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->toResponse($enclosure));
    }

    #[Route('/api/enclosures/{id}/animals', name: 'enclosure_animals', methods: ['GET'])]
    public function getAnimalsInEnclosure(?Enclosure $enclosure, AnimalsRepository $animalsRepository, SerializerInterface $serializer): JsonResponse
    {
        if ($enclosure === null) {
            return new JsonResponse([
                'error' => 'Enclosure not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $animals = $animalsRepository->findBy(['enclosure' => $enclosure]);
        $response = $serializer->serialize($animals, 'json', [
            AbstractNormalizer::ATTRIBUTES => [
                'id',
                'uuid',
                'name',
                'gender',
                'weight',
                'size',
                'age',
                'species' => ['id', 'name', 'clearance', 'diet'],
            ],
            AbstractNormalizer::CIRCULAR_REFERENCE_HANDLER => static fn(object $object): mixed => method_exists($object, 'getId') ? $object->getId() : null,
        ]);

        return new JsonResponse($response, JsonResponse::HTTP_OK, [], true);
    }

    #[OA\Post(
        path: '/api/enclosures',
        summary: 'Create an enclosure',
        tags: ['Enclosure'],
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
                required: ['name', 'clearance', 'positionX', 'positionY', 'size'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Herbivore Habitat B'),
                    new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                    new OA\Property(property: 'positionX', type: 'integer', example: 15),
                    new OA\Property(property: 'positionY', type: 'integer', example: 25),
                    new OA\Property(property: 'size', type: 'integer', example: 120),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: 'Enclosure created'),
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
        ],
    )]
    #[Route('/api/enclosures', name: 'enclosure_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
    ): JsonResponse {
        try {
            /** @var Enclosure $enclosure */
            $enclosure = $serializer->deserialize(
                $request->getContent(),
                Enclosure::class,
                'json',
                [
                    AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
                ],
            );
        } catch (NotEncodableValueException | NotNormalizableValueException | ExtraAttributesException) {
            return new JsonResponse([
                'error' => 'Invalid JSON body.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $name = trim((string) $enclosure->getName());
        if (
            $name === ''
            || $enclosure->getClearance() === null
            || $enclosure->getPositionX() === null
            || $enclosure->getPositionY() === null
            || $enclosure->getSize() === null
        ) {
            return new JsonResponse([
                'error' => 'Fields name, clearance, positionX, positionY, and size are required.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!$this->hasSufficientClearanceForAnimals($enclosure)) {
            return new JsonResponse([
                'error' => 'Enclosure clearance is too low for assigned animals.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $enclosure->setName($name);

        $entityManager->persist($enclosure);
        $entityManager->flush();

        return new JsonResponse([
            ...$this->toResponse($enclosure),
        ], JsonResponse::HTTP_CREATED);
    }

    #[OA\Put(
        path: '/api/enclosures/{id}',
        summary: 'Update an enclosure',
        tags: ['Enclosure'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'clearance', 'positionX', 'positionY', 'size'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Herbivore Habitat B'),
                    new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                    new OA\Property(property: 'positionX', type: 'integer', example: 15),
                    new OA\Property(property: 'positionY', type: 'integer', example: 25),
                    new OA\Property(property: 'size', type: 'integer', example: 120),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: 'Enclosure updated'),
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
                description: 'Enclosure not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Enclosure not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    #[Route('/api/enclosures/{id}', name: 'enclosure_update', methods: ['PUT'])]
    public function update(
        ?Enclosure $enclosure,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
    ): JsonResponse {
        if ($enclosure === null) {
            return new JsonResponse([
                'error' => 'Enclosure not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            /** @var Enclosure $enclosure */
            $enclosure = $serializer->deserialize(
                $request->getContent(),
                Enclosure::class,
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $enclosure,
                    AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
                ],
            );
        } catch (NotEncodableValueException | NotNormalizableValueException | ExtraAttributesException) {
            return new JsonResponse([
                'error' => 'Invalid JSON body.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $name = trim((string) $enclosure->getName());
        if (
            $name === ''
            || $enclosure->getClearance() === null
            || $enclosure->getPositionX() === null
            || $enclosure->getPositionY() === null
            || $enclosure->getSize() === null
        ) {
            return new JsonResponse([
                'error' => 'Fields name, clearance, positionX, positionY, and size are required.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!$this->hasSufficientClearanceForAnimals($enclosure)) {
            return new JsonResponse([
                'error' => 'Enclosure clearance is too low for assigned animals.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $enclosure->setName($name);

        $entityManager->flush();

        return new JsonResponse($this->toResponse($enclosure));
    }

    #[OA\Delete(
        path: '/api/enclosures/{id}',
        summary: 'Delete an enclosure',
        tags: ['Enclosure'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Enclosure deleted'),
            new OA\Response(
                response: 404,
                description: 'Enclosure not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Enclosure not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    #[Route('/api/enclosures/{id}', name: 'enclosure_delete', methods: ['DELETE'])]
    public function delete(?Enclosure $enclosure, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($enclosure === null) {
            return new JsonResponse([
                'error' => 'Enclosure not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($enclosure);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    private function toResponse(Enclosure $enclosure): array
    {
        return [
            'id' => $enclosure->getId(),
            'name' => $enclosure->getName(),
            'clearance' => $enclosure->getClearance()?->value,
            'positionX' => $enclosure->getPositionX(),
            'positionY' => $enclosure->getPositionY(),
            'size' => $enclosure->getSize(),
        ];
    }

    private function hasSufficientClearanceForAnimals(Enclosure $enclosure): bool
    {
        $enclosureClearance = $enclosure->getClearance();
        if ($enclosureClearance === null) {
            return false;
        }

        foreach ($enclosure->getAnimals() as $animal) {
            $speciesClearance = $animal->getSpecies()?->getClearance();

            if ($speciesClearance === null || $enclosureClearance->value < $speciesClearance->value) {
                return false;
            }
        }

        return true;
    }
}
