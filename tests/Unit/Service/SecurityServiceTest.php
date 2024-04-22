<?php

namespace App\Tests\Unit\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Service\SecurityService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\Security;

class SecurityServiceTest extends WebTestCase
{
    public function testCheckUpPermissionSuccessfully(): void
    {
        $fakeUser = $this->createMock(User::class);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($fakeUser);

        $task = new Task();
        $task->setUser($fakeUser);
        $service = new SecurityService($security);
        $AsPermission = $service->checkUpPermission($task);
        $this->assertSame($AsPermission, true, "L'utilisateur doit être assigné à la tâche lors de la création.");
    }

    public function testSecurityFail(): void
    {
        $security = $this->createMock(Security::class);
        $user1 = $this->createMock(User::class);
        $user2 = $this->createMock(User::class);
        $security->method('getUser')->willReturn($user1);

        $task = new Task();
        $task->setUser($user2);
        $service = new SecurityService($security);
        $AsPermission = $service->checkUpPermission($task);
        $this->assertSame($AsPermission, false, "Aucun utilisateur ne doit être assigné à la tâche si aucun n'est connecté.");
    }

    public function testSecurityFailWithException(): void
    {
        $this->expectException(Exception::class);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $task = new Task();
        $service = new SecurityService($security);
        $AsPermission = $service->checkUpPermission($task);
    }
}