<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    public function setUp(): void
    {

    }

    public function testLoginIsUp(): void
    {
        $this->client = static::createClient();
        $urlGenerator = $this->client->getContainer()->get('router.default');
        $this->client->request(Request::METHOD_GET, $urlGenerator->generate('login'));
    }

    public function testLogout(): void
    {
        $client = static::createClient();
    }
}