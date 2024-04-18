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
        private readonly EntityManagerInterface      $manager,
    )
    {
        $this->faker = Factory::create('fr_FR');
    }

    public function load(ObjectManager $manager): void
    {
        $this->createUsersWithTasks(10, 'ROLE_USER');
        $this->createUsersWithTasks(10, 'ROLE_ANONYME');
    }

    private function createUser(string $role): User
    {
        $user = new User;
        $user->setPassword($this->hashed->hashPassword($user, 'password'));
        $user->setUserName($this->faker->word());
        $user->setEmail($this->faker->unique()->safeEmail());
        $user->setRoles([$role]);
        $this->manager->persist($user);

        return $user;
    }

    private function createAdmin(string $role): User
    {
        $AdminUser = new User;
        $AdminUser->setPassword($this->hashed->hashPassword($AdminUser, 'password'));
        $AdminUser->setUserName('admin');
        $AdminUser->setEmail('admin@todo.com');
        $AdminUser->setRoles(['ROLE_ADMIN']);
        $this->manager->persist($AdminUser);

        return $AdminUser;
    }

    private function createTaskForUser(User $user): void
    {
        for ($i = 0; $i < 10; $i++) {
            $task = new Task();
            $task->setTitle($this->faker->sentence());
            $task->setContent($this->faker->sentence());
            $task->setUser($user);
            $this->manager->persist($task);
        }
    }

    private function createUsersWithTasks(int $number, string $role): void
    {
        for ($i = 0; $i < $number; $i++) {
            $user = $this->createUser($role);
            $this->createTaskForUser($user);
        }

        $this->manager->flush();
    }
}
