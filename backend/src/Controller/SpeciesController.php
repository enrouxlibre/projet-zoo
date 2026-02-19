<?php

namespace App\Controller;

use App\Entity\Species;
use App\Enum\ClearanceLevel;
use App\Enum\SpeciesDiet;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class SpeciesController
{
    #[Route('/api/species', name: 'species_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        try {
            $payload = $request->toArray();
        } catch (\Throwable) {
            return new JsonResponse([
                'error' => 'Invalid JSON body.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $name = trim((string) ($payload['name'] ?? ''));
        $dietValue = trim((string) ($payload['diet'] ?? ''));
        $clearanceValue = $payload['clearance'] ?? null;

        if ($name == '' || $dietValue == '' || $clearanceValue === null || !is_numeric($clearanceValue)) {
            return new JsonResponse([
                'error' => 'Fields name, diet, and clearance are required.',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $diet = SpeciesDiet::tryFrom($dietValue);
        if ($diet === null) {
            return new JsonResponse([
                'error' => 'Diet must be carnivorous, herbivorous, or omnivorous.',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $clearance = ClearanceLevel::tryFrom((int) $clearanceValue);
        if ($clearance === null) {
            return new JsonResponse([
                'error' => 'Clearance must be between 1 and 5.',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $species = new Species();
        $species
            ->setName($name)
            ->setDiet($diet)
            ->setClearance($clearance);

        $entityManager->persist($species);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $species->getId(),
            'name' => $species->getName(),
            'diet' => $species->getDiet()?->value,
            'clearance' => $species->getClearance()?->value,
        ], JsonResponse::HTTP_CREATED);
    }
}
