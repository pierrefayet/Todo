<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Task;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
    }

    public function testListTask()
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findOneByEmail('admin@todo.com');
        $this->client->loginUser($user);
        $this->client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }


    public function testToggleActionWithUserTest()
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $TaskRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $user = $userRepository->findOneByEmail('test@test.com');
        $task = $TaskRepository->findOneBy(['user' => $user]);
        $this->client->loginUser($user);
        $this->client->request('GET', uri: "/tasks/{$task->getId()}/toggle");
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }
    public function testEditActionWithUserTest()
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $TaskRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $user = $userRepository->findOneByEmail('test@test.com');
        $task = $TaskRepository->findOneBy(['user' => $user]);
        $this->client->loginUser($user);
        $this->client->request('GET', uri: "/tasks/{$task->getId()}/edit");
        $this->assertResponseIsSuccessful();
    }


    public function testCreateTaskWithUser(): void
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findOneByEmail('test@test.com');
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', 'tasks/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'New Task Title',
            'task[content]' => 'New Task Content'
        ]);
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testCreateTaskWithAdmin(): void
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findOneByEmail('admin@todo.com');
        $this->client->loginUser($user);
        $crawler = $this->client->request('GET', 'tasks/create');
        $form = $crawler->selectButton('Ajouter')->form([
            'task[title]' => 'New Task Title',
            'task[content]' => 'New Task Content'
        ]);
        $this->client->submit($form);

        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testEditTaskWithWrongUserTest()
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $TaskRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $user1 = $userRepository->findOneByEmail('test@test.com');
        $user2 = $userRepository->findOneByEmail('test2@test.com');
        $task = $TaskRepository->findOneBy(['user' => $user1]);
        $this->client->loginUser($user2);
        $this->client->request('GET', uri: "/tasks/{$task->getId()}/edit");
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');

    }
    public function testDeleteActionWithAdminTest()
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $TaskRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $user = $userRepository->findOneByEmail('test@test.com');
        $task = $TaskRepository->findOneBy(['user' => $user]);
        $this->client->loginUser($user);
        $this->client->request('GET', uri: "/tasks/{$task->getId()}/delete");
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testDeleteActionWithOtherUserTest()
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $TaskRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $user1 = $userRepository->findOneByEmail('test@test.com');
        $user2 = $userRepository->findOneByEmail('test2@test.com');
        $task = $TaskRepository->findOneBy(['user' => $user1]);
        $this->client->loginUser($user2);
        $this->client->request('GET', uri: "/tasks/{$task->getId()}/delete");
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }
}