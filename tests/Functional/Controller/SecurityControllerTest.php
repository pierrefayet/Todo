<?php

namespace App\Tests\Functional\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client ??= static::createClient();
    }

    public function testHomepage(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('html', 'To Do List App');
        $this->assertSelectorTextContains('html', 'Nom d\'utilisateur :');
        $this->assertSelectorTextContains('html', 'Mot de passe :');
        $this->assertSelectorTextContains('.btn-success', 'Se connecter');
        $this->assertSelectorTextContains('html', 'Copyright © OpenClassrooms');
    }

    public function testLoginWithValidCredentialsIfIsAdmin(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['username'] = 'admin';
        $form['password'] = 'password';
        $this->client->submit($form);
        $this->assertResponseRedirects('/');
    }

    public function testLoginWithValidCredentialsIfIsUserConnected(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['username'] = 'test';
        $form['password'] = 'password';
        $this->client->submit($form);
        $this->assertResponseRedirects('/');
    }

    public function testLoginWithInvalidCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['username'] = 'jhon';
        $form['password'] = 'wrongPassword';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-danger', 'Invalid credentials.');
    }

    public function testLoginWithoutCredentials(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['username'] = ' ';
        $form['password'] = ' ';
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-danger', 'Invalid credentials.');
    }

    public function testLoginWithCredentialsIsInteger(): void
    {
        $crawler = $this->client->request('GET', '/login');
        $form = $crawler->selectButton('Se connecter')->form();
        $form['username'] = 1234;
        $form['password'] = 1234;
        $this->client->submit($form);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.alert.alert-danger', 'Invalid credentials.');
    }

    public function testLogout(): void
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findOneByEmail('admin@todo.com');
        $this->client->loginUser($user);

        $this->client->request('GET', '/logout');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }
    public function testLogoutIfIsNotConnected(): void
    {
        $userRepository = $this->client->getContainer()->get('doctrine.orm.entity_manager')->getRepository(User::class);
        $user = $userRepository->findOneByEmail('admin@todo.com');
        $this->client->loginUser($user);

        $this->client->request('GET', '/logout');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
        $this->client->followRedirect();
        $location = $this->client->getResponse()->headers->get('Location');
        $this->assertEquals('/login', $location);
    }
}
