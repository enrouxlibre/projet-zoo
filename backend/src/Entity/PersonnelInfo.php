<?php

namespace App\Entity;

use App\Repository\PersonnelInfoRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PersonnelInfoRepository::class)]
class PersonnelInfo
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\OneToOne(inversedBy: 'personnelInfo', cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $userId = null;

    #[ORM\Column(length: 255)]
    private ?string $job = null;

    #[ORM\Column(type: Types::INTEGER)]
    private ?int $clearance = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTime $dateOfBirth = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?User
    {
        return $this->userId;
    }

    public function setUserId(User $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    public function getJob(): ?string
    {
        return $this->job;
    }

    public function setJob(string $job): static
    {
        $this->job = $job;

        return $this;
    }

    public function getClearance(): ?int
    {
        return $this->clearance;
    }

    public function setClearance(int $clearance): static
    {
        $this->clearance = $clearance;

        return $this;
    }

    public function getDateOfBirth(): ?\DateTime
    {
        return $this->dateOfBirth;
    }

    public function setDateOfBirth(\DateTime $dateOfBirth): static
    {
        $this->dateOfBirth = $dateOfBirth;

        return $this;
    }
}
