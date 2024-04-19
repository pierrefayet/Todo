<?php

namespace App\Tests\Functional\Command;

use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

class CreateCustomerAdminCommandTest extends KernelTestCase
{
    public function testExecuteSymfonyCommandSuccessFull(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:create-user');
        $commandTester = new CommandTester($command);
        $commandTester->execute([

            'email' => 'admin1@todo.com',
            'password' => 'password',
        ]);

        $commandTester->assertCommandIsSuccessful();
        $output = $commandTester->getDisplay();
        $this->assertStringContainsString('User created', $output);
    }

    public function testExecuteSymfonyCommandWithEmailIsAInteger(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:create-user');
        $commandTester = new CommandTester($command);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Wrong typehint "email" and "password" arguments');

        $commandTester->execute([

            'email' => 111111,
            'password' => 'password',
        ]);
    }

    public function testExecuteSymfonyCommandWithPasswordIsAInteger(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:create-user');
        $commandTester = new CommandTester($command);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Wrong typehint "email" and "password" arguments');

        $commandTester->execute([

            'email' => 'test@test.com',
            'password' => 1111,
        ]);
    }

    public function testExecuteSymfonyCommandWithEmailAndPasswordIsAInteger(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);

        $command = $application->find('app:create-user');
        $commandTester = new CommandTester($command);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Wrong typehint "email" and "password" arguments');

        $commandTester->execute([

            'email' => 1111,
            'password' => 1111,
        ]);
    }
}
