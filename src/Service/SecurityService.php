<?php

namespace App\Service;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;

readonly class SecurityService
{
    public function __construct(private readonly Security $security)
    {
    }
    public function checkUpPermission(Task $task): bool
    {
        $currentUser = $this->security->getUser();
        if (!$currentUser instanceof User) {
            throw new \Exception('$currentUser n\'est pas une instance de User');
        }
        return $task->getUser() === $currentUser || in_array('ROLE_ADMIN', $currentUser->getRoles());
    }
}
