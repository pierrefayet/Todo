<?php

namespace App\DataFixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private readonly Generator $faker;
    public function __construct(
        private readonly UserPasswordHasherInterface $hashed,
        private readonly EntityManagerInterface $manager,
    )
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 10; $i++) {
            $user = new User;
            $user->setPassword($this->hashed->hashPassword($user, 'password'));
            $user->setUserName($this->faker->word());
            $user->setEmail($this->faker->unique()->safeEmail());
            $user->setRoles(['ROLE_USER']);
            $this->manager->persist($user);

            for ($j = 0; $j < 10; $j++) {
                $task = new Task();
                $task->setTitle($this->faker->sentence());
                $task->setContent($this->faker->sentence());
                $task->setUser($user);
                $this->manager->persist($task);
            }

        }

        $this->manager->flush();
    }
}
