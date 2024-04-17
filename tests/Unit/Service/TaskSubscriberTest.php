<?php

namespace App\Tests\UnitTest\Service;

use App\Entity\Task;
use App\Entity\User;
use App\Service\Tasksubscriber;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\SecurityBundle\Security;

class TaskSubscriberTest extends WebTestCase
{
    public function testSubscribeWithUser()
    {
        $user = $this->createMock(User::class);
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn($user);

        $task = new Task();
        $subscriber = new TaskSubscriber($security);
        $subscriber->subscribe($task);
        $this->assertSame($user, $task->getUser(), "L'utilisateur doit être assigné à la tâche lors de la création.");
    }

    public function testSubscriberWithoutUser()
    {
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $task = new Task();
        $subscriber = new TaskSubscriber($security);
        $subscriber->subscribe($task);
        $this->assertSame($task->getUser(), "Aucun utilisateur ne doit être assigné à la tâche si aucun n'est connecté.");
    }
}