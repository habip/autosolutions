<?php
namespace AppBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\User;

class CarOwnerControllerTest extends WebTestCase
{
    public function testRegistration()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/car-owner/registration/');

        $response = $client->getResponse();
        $this->assertEquals(200, $response->getStatusCode());
    }
}