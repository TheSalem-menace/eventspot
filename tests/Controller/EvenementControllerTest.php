<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EvenementControllerTest extends WebTestCase
{
    public function testIndexPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/evenements');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.event-card, .alert-info');
    }

    public function testHomePage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/');
        $this->assertResponseIsSuccessful();
    }

    public function testNewPageRequiresLogin(): void
    {
        $client = static::createClient();
        $client->request('GET', '/evenements/nouveau');
        // Should redirect to login
        $this->assertResponseRedirects();
    }

    public function testLoginPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/login');
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('form');
    }

    public function testRegisterPage(): void
    {
        $client = static::createClient();
        $client->request('GET', '/register');
        $this->assertResponseIsSuccessful();
    }
}
