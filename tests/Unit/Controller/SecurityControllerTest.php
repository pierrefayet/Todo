<?php

namespace App\Tests\Unit\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser $client;

    public function setUp(): void
    {
        $this->client ??= static::createClient();
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
        $form['username'] = 'quis';
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
        $this->client->request('GET', '/logout');
        $this->assertEquals(302, $this->client->getResponse()->getStatusCode());
    }
}