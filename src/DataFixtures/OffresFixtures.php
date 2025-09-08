<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;

class OffresFixtures extends Fixture
{
    private Generator $faker;
    public function __construct()
    {
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 50; $i++) {
            $offre = new \App\Entity\Offres();
            $offre
                ->setCode($this->faker->unique()->bothify('OFF###??'))
                ->setIntitule("Intitulé de l'offre " . ($i + 1))
                ->setDescription("Description de l'offre " . ($i + 1))
                ->setPrix($this->faker->numberBetween(100, 350))
                ->setDateDebut($this->faker->dateTimeBetween('-1 years', 'now'))
                ->setDateFin($this->faker->dateTimeBetween('now', '+6 months'))
                ->setNbrAdultes($this->faker->numberBetween(1, 2))
                ->setNbrEnfants($this->faker->numberBetween(1, 6))
                ->setIsLocked($this->faker->boolean($i - 2))
                ->setIsPublished(true)
                ->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($offre);
        }
        $manager->flush();
    }
}
