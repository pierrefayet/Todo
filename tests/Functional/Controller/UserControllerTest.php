<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private ?object $urlGenerator;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findOneByEmail('admin@todo.com');
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->loginUser($user);
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
        $form['user[email]'] = 'erdgerg@gmail.com';
        $form['user[roles]'] = "ROLE_USER";
        $this->client->submit($form);
        //dump($form['user[roles]'][0]);
        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-success', 'Superbe ! L\'utilisateur a bien été ajouté.');
    }

    public function testEditUser()
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('user_edit', ['id' => 1]));
        $form = $crawler->selectButton('Modifier')->form();
        $form['user[username]'] = 'newUser';
        $form['user[password][first]'] = 'password';
        $form['user[password][second]'] = 'password';
        $form['user[email]'] = 'newUser@gmail.com';
        $form['user[roles][0]']->tick();
        $this->client->submit($form);

        $this->assertTrue($this->client->getResponse()->isRedirect());
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-success', 'Superbe ! L\'utilisateur a bien été modifié');
    }
}