<?php

namespace App\Controller;

use App\Entity\Animals;
use App\Entity\Enclosure;
use App\Entity\Species;
use App\Enum\Gender;
use App\Repository\AnimalsRepository;
use App\Repository\EnclosureRepository;
use App\Repository\SpeciesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

class AnimalsController
{
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

    #[Route('/api/animals', name: 'animals_create', methods: ['POST'])]
    public function create(
        Request $request,
        SpeciesRepository $speciesRepository,
        EnclosureRepository $enclosureRepository,
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
    ): JsonResponse {
        try {
            /** @var \stdClass $payloadObject */
            $payloadObject = (object) $request->toArray();
            /** @var array<string, mixed> $payload */
            $payload = get_object_vars($payloadObject);
        } catch (\JsonException) {
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
            /** @var \stdClass $payloadObject */
            $payloadObject = (object) $request->toArray();
            /** @var array<string, mixed> $payload */
            $payload = get_object_vars($payloadObject);
        } catch (\JsonException) {
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
        $genderEnum = is_int($gender) ? Gender::tryFrom($gender) : null;

        if (
            !is_string($name)
            || trim($name) === ''
            || $genderEnum === null
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
            ->setGender($genderEnum)
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
            'gender' => $animals->getGender(),
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
