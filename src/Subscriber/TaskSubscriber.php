<?php

namespace App\Subscriber;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, method: 'subscribe', entity: Task::class)]
readonly class TaskSubscriber
{
    public function __construct(private Security $security)
    {
    }
    public function subscribe(Task $task): void
    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            $task->setUser($user);
        }
    }
}
