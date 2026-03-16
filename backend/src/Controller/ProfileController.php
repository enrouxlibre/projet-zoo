<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class ProfileController
{
    public function __construct(
        private readonly Security $security,
    ) {}

    #[Route('/api/profile/{id}', name: 'profile_get', methods: ['GET'])]
    public function getOne(?User $user): JsonResponse
    {
        if ($user === null) {
            return new JsonResponse([
                'error' => 'Profile not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $authenticatedUser = $this->getAuthenticatedUser();
        if (!$authenticatedUser instanceof User) {
            return new JsonResponse([
                'error' => 'Unauthorized.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!$this->canAccessProfile($authenticatedUser, $user)) {
            return new JsonResponse([
                'error' => 'Access denied.',
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $userProfile = $user->getUserProfile();
        if (!$userProfile instanceof UserProfile) {
            return new JsonResponse([
                'error' => 'Profile not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        return new JsonResponse($this->toResponse($user, $userProfile));
    }

    #[Route('/api/profile/{id}', name: 'profile_update', methods: ['PUT'])]
    public function update(?User $user, Request $request, EntityManagerInterface $entityManager): JsonResponse
    {
        if ($user === null) {
            return new JsonResponse([
                'error' => 'Profile not found.',
            ], JsonResponse::HTTP_NOT_FOUND);
        }

        $authenticatedUser = $this->getAuthenticatedUser();
        if (!$authenticatedUser instanceof User) {
            return new JsonResponse([
                'error' => 'Unauthorized.',
            ], JsonResponse::HTTP_UNAUTHORIZED);
        }

        if (!$this->canAccessProfile($authenticatedUser, $user)) {
            return new JsonResponse([
                'error' => 'Access denied.',
            ], JsonResponse::HTTP_FORBIDDEN);
        }

        $userProfile = $user->getUserProfile();
        if (!$userProfile instanceof UserProfile) {
            return new JsonResponse([
                'error' => 'Profile not found.',
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

        $allowedKeys = ['firstName', 'lastName', 'telephone'];
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

        if (
            !is_string($firstName)
            || trim($firstName) === ''
            || !is_string($lastName)
            || trim($lastName) === ''
            || !(is_string($telephone) || $telephone === null)
        ) {
            return new JsonResponse([
                'error' => 'Fields firstName and lastName are required.',
            ], JsonResponse::HTTP_BAD_REQUEST);
        }

        $userProfile
            ->setFirstName(trim($firstName))
            ->setLastName(trim($lastName))
            ->setTelephone($telephone === null ? null : trim($telephone));

        $entityManager->flush();

        return new JsonResponse($this->toResponse($user, $userProfile));
    }

    private function getAuthenticatedUser(): ?User
    {
        $user = $this->security->getUser();

        return $user instanceof User ? $user : null;
    }

    private function canAccessProfile(User $authenticatedUser, User $targetUser): bool
    {
        return $authenticatedUser->getId() === $targetUser->getId() || $this->security->isGranted('ROLE_ADMIN');
    }

    private function toResponse(User $user, UserProfile $userProfile): array
    {
        return [
            'userId' => $user->getId(),
            'email' => $user->getEmail(),
            'firstName' => $userProfile->getFirstName(),
            'lastName' => $userProfile->getLastName(),
            'telephone' => $userProfile->getTelephone(),
        ];
    }
}
