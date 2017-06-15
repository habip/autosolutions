<?php

namespace AppBundle\Tests\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Entity\User;

class MessageControllerTest extends WebTestCase
{
    public function testDialogs()
    {
        $client = static::createClient(array(), array(
                'PHP_AUTH_USER' => 'habip79@mail.ru',
                'PHP_AUTH_PW' => '12345'
        ));

        $crawler = $client->request('GET', '/api/dialogs/');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));

        $crawler = $client->request('GET', '/api/dialogs/?detailedOutput=true');
        $response = $client->getResponse();

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testCreateDialog()
    {
        $client = static::createClient(array(), array(
                'PHP_AUTH_USER' => 'habip79@mail.ru',
                'PHP_AUTH_PW' => '12345'
        ));

        $em = $client->getContainer()->get('doctrine')->getManager();
        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery('select u from AppBundle:User u where u.type = :type')->setMaxResults(1);
        $results = $query->execute(array('type' => User::TYPE_COMPANY));
        $company = $results[0];
        $results = $query->execute(array('type' => User::TYPE_CAR_OWNER));
        $carOwner = $results[0];

        $crawler = $client->request('POST', '/api/dialogs/',
                array(),
                array(),
                array('CONTENT_TYPE' => 'application/json'),
                json_encode(array(
                    'dialog' => array(
                        'participants' => array(
                            array('user' => $company->getId()),
                            array('user' => $carOwner->getId())
                        )
                    )
                ))
        );
        $response = $client->getResponse();

        $this->assertTrue($response->getStatusCode() == 201 || $response->getStatusCode() == 409);
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }

    public function testCreateMessage()
    {
        $client = static::createClient(array(), array(
                'PHP_AUTH_USER' => 'habip79@mail.ru',
                'PHP_AUTH_PW' => '12345'
        ));

        $em = $client->getContainer()->get('doctrine')->getManager();
        /* @var $query \Doctrine\ORM\Query */
        $query = $em->createQuery('select u from AppBundle:User u where u.type = :type')->setMaxResults(1);
        $results = $query->execute(array('type' => User::TYPE_COMPANY));
        $company = $results[0];
        $results = $query->execute(array('type' => User::TYPE_CAR_OWNER));
        $carOwner = $results[0];

        $client->request('POST', '/api/dialogs/',
                array(),
                array(),
                array('CONTENT_TYPE' => 'application/json'),
                json_encode(array(
                        'dialog' => array(
                                'participants' => array(
                                        array('user' => $company->getId()),
                                        array('user' => $carOwner->getId())
                                )
                        )
                ))
        );

        $response = $client->getResponse();
        $data = json_decode($response->getContent(), true);

        $client->request('POST', '/api/dialog/' . $data['id'] . '/messages/',
                array(),
                array(),
                array('CONTENT_TYPE' => 'application/json'),
                json_encode(array(
                        'message' => array(
                                'guid' => uniqid(),
                                'body' => 'test message'
                        )
                ))
        );
        $response = $client->getResponse();
        $this->assertEquals(201, $response->getStatusCode());
        $this->assertSame('application/json', $response->headers->get('Content-Type'));
    }
}
