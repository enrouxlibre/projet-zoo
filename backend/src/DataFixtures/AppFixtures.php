<?php

namespace App\DataFixtures;

use App\Entity\Animals;
use App\Entity\Enclosure;
use App\Entity\PersonnelInfo;
use App\Entity\Species;
use App\Entity\User;
use App\Entity\UserProfile;
use App\Enum\ClearanceLevel;
use App\Enum\Gender;
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
        $user->setRoles(['ROLE_USER', 'ROLE_ADMIN']);
        $user->setPassword(
            $this->passwordHasher->hashPassword($user, 'test1234')
        );

        $userProfile = new UserProfile();
        $userProfile
            ->setUserId($user)
            ->setFirstName('Test')
            ->setLastName('User')
            ->setTelephone('+33100000001');

        $personnelInfo = new PersonnelInfo();
        $personnelInfo
            ->setUserId($user)
            ->setJob('Veterinarian')
            ->setClearance(ClearanceLevel::HIGH)
            ->setDateOfBirth(new \DateTime('1990-01-15'));

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

        $speciesByName = [];

        foreach ($speciesData as $data) {
            $species = new Species();
            $species
                ->setName($data['name'])
                ->setDiet($data['diet'])
                ->setClearance($data['clearance']);

            $manager->persist($species);
            $speciesByName[$data['name']] = $species;
        }

        $enclosuresData = [
            ['name' => 'Carnivore Zone Alpha', 'clearance' => ClearanceLevel::CRITICAL, 'positionX' => 10, 'positionY' => 15, 'size' => 120],
            ['name' => 'Herbivore Plains', 'clearance' => ClearanceLevel::LOW, 'positionX' => 35, 'positionY' => 20, 'size' => 200],
            ['name' => 'Omnivore Habitat', 'clearance' => ClearanceLevel::MODERATE, 'positionX' => 60, 'positionY' => 25, 'size' => 140],
            ['name' => 'Raptor Compound', 'clearance' => ClearanceLevel::HIGH, 'positionX' => 20, 'positionY' => 55, 'size' => 110],
            ['name' => 'Waterfront Paddock', 'clearance' => ClearanceLevel::MODERATE, 'positionX' => 75, 'positionY' => 45, 'size' => 170],
        ];

        $enclosuresByName = [];

        foreach ($enclosuresData as $data) {
            $enclosure = new Enclosure();
            $enclosure
                ->setName($data['name'])
                ->setClearance($data['clearance'])
                ->setPositionX($data['positionX'])
                ->setPositionY($data['positionY'])
                ->setSize($data['size']);

            $manager->persist($enclosure);
            $enclosuresByName[$data['name']] = $enclosure;
        }

        $animalsData = [
            ['name' => 'Rexy', 'gender' => Gender::FEMALE, 'weight' => 7000, 'size' => 13, 'age' => 12, 'species' => 'Tyrannosaurus rex', 'enclosure' => 'Carnivore Zone Alpha'],
            ['name' => 'Stormclaw', 'gender' => Gender::MALE, 'weight' => 90, 'size' => 2, 'age' => 6, 'species' => 'Velociraptor', 'enclosure' => 'Raptor Compound'],
            ['name' => 'AquaFang', 'gender' => Gender::FEMALE, 'weight' => 8200, 'size' => 15, 'age' => 14, 'species' => 'Spinosaurus', 'enclosure' => 'Carnivore Zone Alpha'],
            ['name' => 'Trike', 'gender' => Gender::MALE, 'weight' => 6000, 'size' => 9, 'age' => 10, 'species' => 'Triceratops', 'enclosure' => 'Waterfront Paddock'],
            ['name' => 'Spike', 'gender' => Gender::FEMALE, 'weight' => 5000, 'size' => 8, 'age' => 11, 'species' => 'Stegosaurus', 'enclosure' => 'Herbivore Plains'],
            ['name' => 'Tank', 'gender' => Gender::MALE, 'weight' => 7000, 'size' => 7, 'age' => 13, 'species' => 'Ankylosaurus', 'enclosure' => 'Omnivore Habitat'],
            ['name' => 'Swiftfoot', 'gender' => Gender::FEMALE, 'weight' => 450, 'size' => 4, 'age' => 5, 'species' => 'Gallimimus', 'enclosure' => 'Herbivore Plains'],
        ];

        foreach ($animalsData as $data) {
            $species = $speciesByName[$data['species']] ?? null;
            $enclosure = $enclosuresByName[$data['enclosure']] ?? null;

            if (!$species instanceof Species || !$enclosure instanceof Enclosure) {
                throw new \RuntimeException('Unable to seed animals: species or enclosure not found.');
            }

            $speciesClearance = $species->getClearance();
            $enclosureClearance = $enclosure->getClearance();

            if ($speciesClearance === null || $enclosureClearance === null || $enclosureClearance->value < $speciesClearance->value) {
                throw new \RuntimeException('Unable to seed animals: enclosure clearance is lower than species clearance.');
            }

            $animal = new Animals();
            $animal
                ->setName($data['name'])
                ->setGender($data['gender'])
                ->setWeight($data['weight'])
                ->setSize($data['size'])
                ->setAge($data['age'])
                ->setSpecies($species)
                ->setEnclosure($enclosure);

            $manager->persist($animal);
        }

        $usersData = [
            ['email' => 'user1@example.com', 'firstName' => 'Alice', 'lastName' => 'Martin', 'telephone' => '+33100000002', 'job' => 'Ranger', 'clearance' => ClearanceLevel::MODERATE, 'dateOfBirth' => '1988-02-03'],
            ['email' => 'user2@example.com', 'firstName' => 'Bruno', 'lastName' => 'Lefevre', 'telephone' => '+33100000003', 'job' => 'Security Officer', 'clearance' => ClearanceLevel::CRITICAL, 'dateOfBirth' => '1985-06-21', 'roles' => ['ROLE_ADMIN']],
            ['email' => 'user3@example.com', 'firstName' => 'Claire', 'lastName' => 'Dupont', 'telephone' => '+33100000004', 'job' => 'Biologist', 'clearance' => ClearanceLevel::HIGH, 'dateOfBirth' => '1992-11-10'],
            ['email' => 'user4@example.com', 'firstName' => 'David', 'lastName' => 'Moreau', 'telephone' => '+33100000005', 'job' => 'Veterinarian', 'clearance' => ClearanceLevel::HIGH, 'dateOfBirth' => '1991-09-05'],
            ['email' => 'user5@example.com', 'firstName' => 'Emma', 'lastName' => 'Petit', 'telephone' => '+33100000006', 'job' => 'Animal Caretaker', 'clearance' => ClearanceLevel::LOW, 'dateOfBirth' => '1995-04-17'],
            ['email' => 'user6@example.com', 'firstName' => 'Fabien', 'lastName' => 'Lambert', 'telephone' => '+33100000007', 'job' => 'Guide', 'clearance' => ClearanceLevel::MINIMAL, 'dateOfBirth' => '1993-07-29'],
            ['email' => 'user7@example.com', 'firstName' => 'Gaelle', 'lastName' => 'Renaud', 'telephone' => '+33100000008', 'job' => 'Nutritionist', 'clearance' => ClearanceLevel::MODERATE, 'dateOfBirth' => '1989-12-12'],
            ['email' => 'user8@example.com', 'firstName' => 'Hugo', 'lastName' => 'Chevalier', 'telephone' => '+33100000009', 'job' => 'Maintenance Technician', 'clearance' => ClearanceLevel::LOW, 'dateOfBirth' => '1987-03-08'],
            ['email' => 'user9@example.com', 'firstName' => 'Ines', 'lastName' => 'Fontaine', 'telephone' => '+33100000010', 'job' => 'Research Analyst', 'clearance' => ClearanceLevel::MODERATE, 'dateOfBirth' => '1994-10-01'],
            ['email' => 'user10@example.com', 'firstName' => 'Julien', 'lastName' => 'Garcia', 'telephone' => '+33100000011', 'job' => 'Operations Manager', 'clearance' => ClearanceLevel::CRITICAL, 'dateOfBirth' => '1986-05-26'],
        ];

        foreach ($usersData as $data) {
            $seededUser = new User();
            $seededUser
                ->setEmail($data['email'])
                ->setRoles($data['roles'] ?? ['ROLE_USER'])
                ->setPassword(
                    $this->passwordHasher->hashPassword($seededUser, 'test1234')
                );

            $seededUserProfile = new UserProfile();
            $seededUserProfile
                ->setUserId($seededUser)
                ->setFirstName($data['firstName'])
                ->setLastName($data['lastName'])
                ->setTelephone($data['telephone']);

            $seededPersonnelInfo = new PersonnelInfo();
            $seededPersonnelInfo
                ->setUserId($seededUser)
                ->setJob($data['job'])
                ->setClearance($data['clearance'])
                ->setDateOfBirth(new \DateTime($data['dateOfBirth']));

            $manager->persist($seededUser);
            $manager->persist($seededUserProfile);
            $manager->persist($seededPersonnelInfo);
        }

        $manager->persist($user);
        $manager->persist($userProfile);
        $manager->persist($personnelInfo);
        $manager->flush();
    }
}
