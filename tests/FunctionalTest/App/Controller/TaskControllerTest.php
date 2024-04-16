<?php

namespace App\Tests\FunctionalTest\App\Controller;

use App\Controller\SecurityController;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findOneByEmail('admin@todo.com');
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->loginUser($user);
    }

    public function testEditActionWithAdminTest()
    {
        $this->client->request('GET', '/tasks/1/edit');
        $this->assertResponseIsSuccessful();
        $this->client->followRedirect();
        $this->assertSelectorTextContains('form', 'Edit Task');
    }


    public function createActionTest(): void
    {
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $url = $urlGenerator->generate('task_create');

        $crawler = $this->client->request(Request::METHOD_GET, $url);
        $form = $crawler->selectButton('Save Task')->form([
            'task[title]' => 'New Task Title',
            'task[content]' => 'New Task Content',
        ]);
        $this->client->submit($form);

        $this->assertResponseRedirects();
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.success', 'Vous n\'êtes pas autorisé à modifier cette tâche.');
    }
}