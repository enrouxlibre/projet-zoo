<?php

namespace App\Controller;

use App\Entity\Species;
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
        $diet = trim((string) ($payload['diet'] ?? ''));
        $clearanceValue = $payload['clearance'] ?? null;

        if ($name == '' || $diet == '' || $clearanceValue === null || !is_numeric($clearanceValue)) {
            return new JsonResponse([
                'error' => 'Fields name, diet, and clearance are required.',
            ], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $species = new Species();
        $species
            ->setName($name)
            ->setDiet($diet)
            ->setClearance((int) $clearanceValue);

        $entityManager->persist($species);
        $entityManager->flush();

        return new JsonResponse([
            'id' => $species->getId(),
            'name' => $species->getName(),
            'diet' => $species->getDiet(),
            'clearance' => $species->getClearance(),
        ], JsonResponse::HTTP_CREATED);
    }
}
