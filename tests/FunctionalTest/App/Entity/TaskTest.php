<?php

namespace App\Tests\TestFunctionnal\App\Entity;

use App\Entity\Task;
use PHPUnit\Framework\TestCase;

class TaskTest extends TestCase
{
    public function testToggle()
    {
        $task = new Task();
        $task->setIsDone(false);
        $task->toggle(true);

        $this->assertTrue($task->isDone());
    }
}
