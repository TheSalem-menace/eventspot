<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EvenementApiTest extends WebTestCase
{
    public function testApiEvenementsCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/evenements', [], [], ['HTTP_ACCEPT' => 'application/json']);
        $this->assertResponseIsSuccessful();
        $this->assertResponseHeaderSame('content-type', 'application/json; charset=utf-8');
    }

    public function testApiLieuxCollection(): void
    {
        $client = static::createClient();
        $client->request('GET', '/api/lieux', [], [], ['HTTP_ACCEPT' => 'application/json']);
        $this->assertResponseIsSuccessful();
    }
}
