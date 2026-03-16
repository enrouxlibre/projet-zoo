<?php

namespace App\Controller;

use App\Entity\PersonnelInfo;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Enum\ClearanceLevel;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PersonnelController
{
    #[Route('/api/personnel', name: 'personnel_list', methods: ['GET'])]
    public function list(UserRepository $userRepository): JsonResponse
    {
        $users = $userRepository->findBy([], ['id' => 'ASC']);

        $payload = [];
        foreach ($users as $user) {
            if (!$user instanceof User) {
                continue;
            }

            $userProfile = $user->getUserProfile();
            $personnelInfo = $user->getPersonnelInfo();

            if (!$userProfile instanceof UserProfile || !$personnelInfo instanceof PersonnelInfo) {
                continue;
            }

            $payload[] = $this->toResponse($user, $userProfile, $personnelInfo);
        }

        return new JsonResponse($payload);
    }

    #[Route('/api/personnel/{id}', name: 'personnel_get', methods: ['GET'])]
    public function getOne(?User $user): JsonResponse
    {
        if ($user === null) {
            return new JsonResponse([
                'error' => 'Personnel entry not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $userProfile = $user->getUserProfile();
        $personnelInfo = $user->getPersonnelInfo();
        if (!$userProfile instanceof UserProfile || !$personnelInfo instanceof PersonnelInfo) {
            return new JsonResponse([
                'error' => 'Personnel entry not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->toResponse($user, $userProfile, $personnelInfo));
    }

    #[Route('/api/personnel/{id}', name: 'personnel_update', methods: ['PUT'])]
    public function update(?User $user, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($user === null) {
            return new JsonResponse([
                'error' => 'Personnel entry not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $userProfile = $user->getUserProfile();
        $personnelInfo = $user->getPersonnelInfo();
        if (!$userProfile instanceof UserProfile || !$personnelInfo instanceof PersonnelInfo) {
            return new JsonResponse([
                'error' => 'Personnel entry not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        try {
            /** @var mixed $payload */
            $payload = $request->toArray();
        } catch (\JsonException) {
            return new JsonResponse([
                'error' => 'Invalid JSON body.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        if (!is_array($payload)) {
            return new JsonResponse([
                'error' => 'Invalid JSON body.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $allowedKeys = ['firstName', 'lastName', 'telephone', 'job', 'clearance', 'dateOfBirth'];
        foreach (array_keys($payload) as $key) {
            if (!in_array($key, $allowedKeys, true)) {
                return new JsonResponse([
                    'error' => 'Invalid JSON body.',
                ], JsonResponse::HTTP_BAD_REQUEST);
            }
        }

        $firstName = $payload['firstName'] ?? null;
        $lastName = $payload['lastName'] ?? null;
        $telephone = $payload['telephone'] ?? null;
        $job = $payload['job'] ?? null;
        $clearance = $payload['clearance'] ?? null;
        $dateOfBirth = $payload['dateOfBirth'] ?? null;

        if (
            !is_string($firstName)
            || trim($firstName) === ''
            || !is_string($lastName)
            || trim($lastName) === ''
            || !(is_string($telephone) || $telephone === null)
            || !is_string($job)
            || trim($job) === ''
            || !is_int($clearance)
            || !is_string($dateOfBirth)
            || trim($dateOfBirth) === ''
        ) {
            return new JsonResponse([
                'error' => 'Fields firstName, lastName, telephone, job, clearance, and dateOfBirth are required.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $clearanceLevel = ClearanceLevel::tryFrom($clearance);
        if ($clearanceLevel === null) {
            return new JsonResponse([
                'error' => 'Invalid clearance value.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        try {
            $birthDate = new \DateTime($dateOfBirth);
        } catch (\Exception) {
            return new JsonResponse([
                'error' => 'Invalid dateOfBirth value. Expected a valid date.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $userProfile
            ->setFirstName(trim($firstName))
            ->setLastName(trim($lastName))
            ->setTelephone($telephone === null ? null : trim($telephone));

        $personnelInfo
            ->setJob(trim($job))
            ->setClearance($clearanceLevel)
            ->setDateOfBirth($birthDate);

        $entityManager->flush();

        return new JsonResponse($this->toResponse($user, $userProfile, $personnelInfo));
    }

    private function toResponse(User $user, UserProfile $userProfile, PersonnelInfo $personnelInfo): array
    {
        return [
            'userId' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $userProfile->getFirstName(),
            'lastName' => $userProfile->getLastName(),
            'telephone' => $userProfile->getTelephone(),
            'job' => $personnelInfo->getJob(),
            'clearance' => $personnelInfo->getClearance()?->value,
            'dateOfBirth' => $personnelInfo->getDateOfBirth()?->format('Y-m-d'),
        ];
    }
}
