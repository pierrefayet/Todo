<?php

namespace Fixtures;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class Fixtures extends Fixture
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
        $this->createUsersWithTasks('ROLE_USER');
        $this->createUsersWithTasks('ROLE_ANONYME');
        $this->createAdmin();
        $this->createTaskForUserTest($this-> createUserTest());
        $this->createOtherUserTest();
        $this->manager->flush();

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

    private function createUserTest(): User
    {
        $testUser = new User;
        $testUser->setPassword($this->hashed->hashPassword($testUser, 'password'));
        $testUser->setUserName('test');
        $testUser->setEmail('test@test.com');
        $testUser->setRoles(['ROLE_USER']);
        $this->manager->persist($testUser);

        return $testUser;
    }

    private function createOtherUserTest(): User
    {
        $testUser = new User;
        $testUser->setPassword($this->hashed->hashPassword($testUser, 'password'));
        $testUser->setUserName('test2');
        $testUser->setEmail('test2@test.com');
        $testUser->setRoles(['ROLE_USER']);
        $this->manager->persist($testUser);

        return $testUser;
    }

    private function createTaskForUserTest(User $user): void
    {
            $task = new Task();
            $task->setTitle($this->faker->sentence());
            $task->setContent($this->faker->sentence());
            $task->setUser($user);
            $this->manager->persist($task);
    }

    private function createAdmin(): void
    {
        $adminUser = new User;
        $adminUser->setPassword($this->hashed->hashPassword($adminUser, 'password'));
        $adminUser->setUserName('admin');
        $adminUser->setEmail('admin@todo.com');
        $adminUser->setRoles(['ROLE_ADMIN']);
        $this->manager->persist($adminUser);

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

    private function createUsersWithTasks(string $role): void
    {
        for ($i = 0; $i < 10; $i++) {
            $user = $this->createUser($role);
            $this->createTaskForUser($user);
        }
    }
}
