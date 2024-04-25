<?php

namespace App\Tests\Unit\Repository;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Persistence\ManagerRegistry;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class UserRepositoryTest extends TestCase
{
    private EntityManagerInterface $entityManagerMock;
    private UserRepository $userRepository;

    protected function setUp(): void
    {
        $this->entityManagerMock = $this->createMock(EntityManagerInterface::class);
        $classMetadata = $this->createMock(ClassMetadata::class);
        $classMetadata->name = User::class;
        $this->entityManagerMock->method('getClassMetadata')
            ->with(User::class)
            ->willReturn($classMetadata);
        $managerRegistryMock = $this->createMock(ManagerRegistry::class);
        $managerRegistryMock->method('getManagerForClass')
            ->with(User::class)
            ->willReturn($this->entityManagerMock);
        $this->userRepository = new UserRepository($managerRegistryMock);
    }

    public function testUpgradePasswordWithUnsupportedUser(): void
    {
        $testUser = $this->createMock(PasswordAuthenticatedUserInterface::class);
        $this->expectException(UnsupportedUserException::class);
        $this->expectExceptionMessage(sprintf('Instances of "%s" are not supported.', get_class($testUser)));
        $this->userRepository->upgradePassword($testUser, 'newhashedpassword');
    }

    public function testUpgradePasswordWithUser(): void
    {
        $user = new User();
        $user->setPassword('oldpassword');
        $this->entityManagerMock->expects($this->once())
            ->method('persist')
            ->with($user);
        $this->entityManagerMock->expects($this->once())
            ->method('flush');

        $this->userRepository->upgradePassword($user, 'newhashedpassword');
        $this->assertEquals('newhashedpassword', $user->getPassword());
    }
}
