<?php

namespace App\Tests\Functional\Controller;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private ?object $urlGenerator;
    private User $user;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $this->user = $userRepository->findOneByEmail('admin@todo.com');
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->loginUser($this->user);
    }

    public function testListUser()
    {
        $this->client->request(Request::METHOD_POST, $this->urlGenerator->generate('user_create'));
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
    }

    public function testCreateUser()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_create'));

        $form = $crawler->selectButton('Ajouter')->form();
        $form['user[username]'] = 'test';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'ergerg@gmail.com';
        $form['user[roles]'][1]->tick();
        $this->client->submit($form);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-success', 'Superbe ! L\'utilisateur a bien été ajouté.');
    }

    public function testEditUser()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_edit', ['id' => $this->user->getId()]));
        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'newUser';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'newUser@gmail.com';
        $form['user[roles]'][1]->tick();
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-success', 'Superbe ! L\'utilisateur a bien été modifié');
    }

    public function testDeleteUserWithAdminTest()
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findOneByEmail('admin@todo.com');
        $user1 = $userRepository->findOneByEmail('test@test.com');
        $this->client->loginUser($user);
        $this->client->request('GET', uri: "/users/{$user1->getId()}/delete");
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-success', 'Superbe ! L\'utilisateur a bien été supprimée.');
    }

    public function testDeleteUserWithUserAndCheckIfIsDeleteInBaseAndHisTask(): void
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findOneByEmail('admin@todo.com');
        $this->client->loginUser($user);
        $user1 = $userRepository->findOneByEmail('test@test.com');
        $taskRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(Task::class);
        $countTaskBeforeAdded = count($taskRepository->findAll());
        $countUserBeforeAdded = count($userRepository->findAll());
        $this->client->loginUser($user);
        $this->client->request('GET', uri: "/users/{$user1->getId()}/delete");
        $countTaskAfterAdded = count($taskRepository->findAll());
        $countUserAfterAdded = count($userRepository->findAll());
        $this->assertEquals($countTaskBeforeAdded - 1, $countTaskAfterAdded);
        $this->assertEquals($countUserBeforeAdded - 1, $countUserAfterAdded);
    }
}
