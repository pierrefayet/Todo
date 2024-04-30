<?php

namespace App\Tests\Unit\Subscriber;

use App\Entity\Task;
use App\Entity\User;
use App\Subscriber\Tasksubscriber;
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
        $this->assertSame($user, $task->getUser());
    }

    public function testSubscriberWithoutUser()
    {
        $security = $this->createMock(Security::class);
        $security->method('getUser')->willReturn(null);

        $task = new Task();
        $subscriber = new TaskSubscriber($security);
        $subscriber->subscribe($task);
        $this->assertSame(null, $task->getUser());
    }
}
