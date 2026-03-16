<?php

namespace App\Entity;

use App\Enum\ClearanceLevel;
use App\Enum\SpeciesDiet;
use App\Repository\SpeciesRepository;
use BcMath\Number;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpeciesRepository::class)]
class Species
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(enumType: ClearanceLevel::class)]
    private ?ClearanceLevel $clearance = null;

    #[ORM\Column(enumType: SpeciesDiet::class)]
    private ?SpeciesDiet $diet = null;

    /**
     * @var Collection<int, Animals>
     */
    #[ORM\OneToMany(targetEntity: Animals::class, mappedBy: 'species')]
    private Collection $animals;

    public function __construct()
    {
        $this->animals = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getClearance(): ?ClearanceLevel
    {
        return $this->clearance;
    }

    public function setClearance(ClearanceLevel $clearance): static
    {
        $this->clearance = $clearance;

        return $this;
    }

    public function getDiet(): ?SpeciesDiet
    {
        return $this->diet;
    }

    public function setDiet(SpeciesDiet $diet): static
    {
        $this->diet = $diet;

        return $this;
    }

    /**
     * @return Collection<int, Animals>
     */
    public function getAnimals(): Collection
    {
        return $this->animals;
    }

    public function addAnimal(Animals $animal): static
    {
        if (!$this->animals->contains($animal)) {
            $this->animals->add($animal);
            $animal->setSpecies($this);
        }

        return $this;
    }

    public function removeAnimal(Animals $animal): static
    {
        if ($this->animals->removeElement($animal)) {
            // set the owning side to null (unless already changed)
            if ($animal->getSpecies() === $this) {
                $animal->setSpecies(null);
            }
        }

        return $this;
    }
}
