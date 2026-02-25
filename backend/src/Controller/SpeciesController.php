<?php

namespace App\Controller;

use App\Entity\Species;
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

class SpeciesController
{
    #[OA\Get(
        path: '/api/species',
        summary: 'List all species',
        tags: ['Species'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Species list',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Tyrannosaurus rex'),
                            new OA\Property(property: 'diet', type: 'string', enum: ['carnivorous', 'herbivorous', 'omnivorous']),
                            new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                        ],
                        type: 'object',
                    ),
                ),
            ),
        ],
    )]
    #[Route('/api/species', name: 'species_list', methods: ['GET'])]
    public function list(SpeciesRepository $speciesRepository): JsonResponse
    {
        $speciesList = $speciesRepository->findBy([], ['id' => 'ASC']);

        return new JsonResponse(array_map(
            fn(Species $species): array => $this->toResponse($species),
            $speciesList,
        ));
    }

    #[OA\Get(
        path: '/api/species/{id}',
        summary: 'Get one species',
        tags: ['Species'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Species found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Tyrannosaurus rex'),
                        new OA\Property(property: 'diet', type: 'string', enum: ['carnivorous', 'herbivorous', 'omnivorous']),
                        new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                    ],
                    type: 'object',
                ),
            ),
            new OA\Response(
                response: 404,
                description: 'Species not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Species not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    #[Route('/api/species/{id}', name: 'species_get', methods: ['GET'])]
    public function getOne(?Species $species): JsonResponse
    {
        if ($species === null) {
            return new JsonResponse([
                'error' => 'Species not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->toResponse($species));
    }

    #[OA\Post(
        path: '/api/species',
        summary: 'Create a species',
        tags: ['Species'],
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
                required: ['name', 'diet', 'clearance'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Allosaurus'),
                    new OA\Property(property: 'diet', type: 'string', enum: ['carnivorous', 'herbivorous', 'omnivorous']),
                    new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(response: 201, description: 'Species created'),
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
    #[Route('/api/species', name: 'species_create', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
    ): JsonResponse {
        try {
            /** @var Species $species */
            $species = $serializer->deserialize(
                $request->getContent(),
                Species::class,
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

        $name = trim((string) $species->getName());
        if ($name === '' || $species->getDiet() === null || $species->getClearance() === null) {
            return new JsonResponse([
                'error' => 'Fields name, diet, and clearance are required.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $species->setName($name);

        $entityManager->persist($species);
        $entityManager->flush();

        return new JsonResponse([
            ...$this->toResponse($species),
        ], JsonResponse::HTTP_CREATED);
    }

    #[OA\Put(
        path: '/api/species/{id}',
        summary: 'Update a species',
        tags: ['Species'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'diet', 'clearance'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Allosaurus'),
                    new OA\Property(property: 'diet', type: 'string', enum: ['carnivorous', 'herbivorous', 'omnivorous']),
                    new OA\Property(property: 'clearance', type: 'integer', enum: [1, 2, 3, 4, 5]),
                ],
                type: 'object',
            ),
        ),
        responses: [
            new OA\Response(response: 200, description: 'Species updated'),
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
                description: 'Species not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Species not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    #[Route('/api/species/{id}', name: 'species_update', methods: ['PUT'])]
    public function update(
        ?Species $species,
        Request $request,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
    ): JsonResponse {
        if ($species === null) {
            return new JsonResponse([
                'error' => 'Species not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            /** @var Species $species */
            $species = $serializer->deserialize(
                $request->getContent(),
                Species::class,
                'json',
                [
                    AbstractNormalizer::OBJECT_TO_POPULATE => $species,
                    AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
                ],
            );
        } catch (NotEncodableValueException | NotNormalizableValueException | ExtraAttributesException) {
            return new JsonResponse([
                'error' => 'Invalid JSON body.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $name = trim((string) $species->getName());
        if ($name === '' || $species->getDiet() === null || $species->getClearance() === null) {
            return new JsonResponse([
                'error' => 'Fields name, diet, and clearance are required.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $species->setName($name);

        $entityManager->flush();

        return new JsonResponse($this->toResponse($species));
    }

    #[OA\Delete(
        path: '/api/species/{id}',
        summary: 'Delete a species',
        tags: ['Species'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(response: 204, description: 'Species deleted'),
            new OA\Response(
                response: 404,
                description: 'Species not found',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'error', type: 'string', example: 'Species not found.'),
                    ],
                    type: 'object',
                ),
            ),
        ],
    )]
    #[Route('/api/species/{id}', name: 'species_delete', methods: ['DELETE'])]
    public function delete(?Species $species, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($species === null) {
            return new JsonResponse([
                'error' => 'Species not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $entityManager->remove($species);
        $entityManager->flush();

        return new JsonResponse(null, JsonResponse::HTTP_NO_CONTENT);
    }

    private function toResponse(Species $species): array
    {
        return [
            'id' => $species->getId(),
            'name' => $species->getName(),
            'diet' => $species->getDiet()?->value,
            'clearance' => $species->getClearance()?->value,
        ];
    }
}
