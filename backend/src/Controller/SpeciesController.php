<?php

namespace App\Controller;

use App\Entity\Species;
use App\Repository\SpeciesRepository;
use Doctrine\ORM\EntityManagerInterface;
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
    #[Route('/api/species', name: 'species_list', methods: ['GET'])]
    public function list(SpeciesRepository $speciesRepository): JsonResponse
    {
        $speciesList = $speciesRepository->findBy([], ['id' => 'ASC']);

        return new JsonResponse(array_map(
            fn(Species $species): array => $this->toResponse($species),
            $speciesList,
        ));
    }

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
