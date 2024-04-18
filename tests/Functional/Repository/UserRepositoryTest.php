<?php

namespace App\Tests\Functional\Repository;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;

class UserRepositoryTest extends KernelTestCase
{
    private $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->entityManager = $kernel->getContainer()->get('doctrine')->getManager();
    }

    public function testUpgradePasswordSuccess(): void
    {
        $user = new User();
        $user->setUsername('test_user');
        $user->setEmail('test@test.com');
        $user->setPassword('existing_password');
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        $userRepository = $this->entityManager->getRepository(User::class);
        $userRepository->upgradePassword($user, 'new_hashed_password');

        $updatedUser = $userRepository->find($user->getId());

        $this->assertSame('new_hashed_password', $updatedUser->getPassword());
    }
}