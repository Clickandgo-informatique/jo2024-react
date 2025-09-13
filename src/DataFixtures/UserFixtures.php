<?php

namespace App\DataFixtures;

use App\Entity\Users;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private Generator $faker;
    public function __construct(private UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
        $this->faker = Factory::create('fr_FR');
    }
    public function load(ObjectManager $manager): void
    {
        //Création d'un administrateur
        $admin = new Users();

        $admin
            ->setNickname('admin')
            ->setPassword($this->passwordHasher->hashPassword($admin, 'Admin!447'))
            ->setEmail('admin@jo2024.fr')
            ->setRoles(['ROLE_ADMIN'])
             ->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($admin);

        //Création d'utilisateurs factices
        for ($i = 0; $i < 10; $i++) {

            $user = new Users();
            $user->setNickname($this->faker->username())
                ->setEmail($this->faker->email())
                ->setPassword($this->passwordHasher->hashPassword($user, 'Jo2024!'))
                ->setRoles(['ROLE_USER'])
                 ->setCreatedAt(new \DateTimeImmutable());
            $manager->persist($user);
        }
        $manager->flush();
    }
}
