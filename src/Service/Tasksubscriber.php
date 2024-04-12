<?php

namespace App\Service;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Events;
use Symfony\Bundle\SecurityBundle\Security;

#[AsEntityListener(event: Events::prePersist, method: 'subscribe', entity: Task::class)]
readonly class Tasksubscriber
{
    public function __construct(private Security $security)
    {
    }
    public function subscribe(Task $task)
    {
        if($this->security->getUser() !== null) {
            $task = $task->setUser($this->security->getUser());
        }

    }
}