<?php

namespace App\Tests\Unit\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findOneByEmail('admin@todo.com');
        $this->client->loginUser($user);
    }

    public function testListTask() {
        $this->client->request('GET', '/');
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testEditActionWithAdminTest()
    {
        $this->client->request('GET', '/tasks/1/edit');
        $this->assertResponseIsSuccessful();
        $this->client->followRedirect();
        $this->assertSelectorTextContains('form', 'Edit Task');
    }


    public function testCreateTask(): void
    {
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
}