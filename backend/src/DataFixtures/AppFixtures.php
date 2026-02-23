<?php

namespace App\DataFixtures;

use App\Entity\Species;
use App\Entity\User;
use App\Enum\ClearanceLevel;
use App\Enum\SpeciesDiet;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher,
    ) {}

    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('test@example.com');
        $user->setRoles(['ROLE_USER']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'test1234')
        );

        $speciesData = [
            ['name' => 'Tyrannosaurus rex', 'diet' => SpeciesDiet::CARNIVOROUS, 'clearance' => ClearanceLevel::CRITICAL],
            ['name' => 'Velociraptor', 'diet' => SpeciesDiet::CARNIVOROUS, 'clearance' => ClearanceLevel::HIGH],
            ['name' => 'Spinosaurus', 'diet' => SpeciesDiet::CARNIVOROUS, 'clearance' => ClearanceLevel::CRITICAL],
            ['name' => 'Triceratops', 'diet' => SpeciesDiet::HERBIVOROUS, 'clearance' => ClearanceLevel::MODERATE],
            ['name' => 'Stegosaurus', 'diet' => SpeciesDiet::HERBIVOROUS, 'clearance' => ClearanceLevel::LOW],
            ['name' => 'Brachiosaurus', 'diet' => SpeciesDiet::HERBIVOROUS, 'clearance' => ClearanceLevel::LOW],
            ['name' => 'Ankylosaurus', 'diet' => SpeciesDiet::HERBIVOROUS, 'clearance' => ClearanceLevel::MODERATE],
            ['name' => 'Pachycephalosaurus', 'diet' => SpeciesDiet::HERBIVOROUS, 'clearance' => ClearanceLevel::LOW],
            ['name' => 'Oviraptor', 'diet' => SpeciesDiet::OMNIVOROUS, 'clearance' => ClearanceLevel::MODERATE],
            ['name' => 'Gallimimus', 'diet' => SpeciesDiet::OMNIVOROUS, 'clearance' => ClearanceLevel::MINIMAL],
        ];

        foreach ($speciesData as $data) {
            $species = new Species();
            $species
                ->setName($data['name'])
                ->setDiet($data['diet'])
                ->setClearance($data['clearance']);

            $manager->persist($species);
        }

        $manager->persist($user);
        $manager->flush();
    }
}
