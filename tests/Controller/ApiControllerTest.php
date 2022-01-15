<?php

declare(strict_types=1);

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ApiControllerTest extends WebTestCase
{
    public function testAllAttributes(): void
    {
        $client = static::createClient();
        $client->request('GET', '/parameter');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(
            '{"Add":["high","low","medium"],"Axis":["0","180","90"],"Color":["Blue","Green"],"Cyl":["-0.75","-1.50","-2.25"],"Sph":["-20.00","+20.00","0.00"]}',
            $client->getResponse()->getContent()
        );
    }

    public function testSphMinus20(): void
    {
        $client = static::createClient();
        $client->request('GET', '/parameter?Sph=-20.00');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(
            '{"Cyl":["-1.50","-2.25"],"Sph":["-20.00"]}',
            $client->getResponse()->getContent()
        );
    }

    public function testCylMinus225(): void
    {
        $client = static::createClient();
        $client->request('GET', '/parameter?Cyl=-2.25');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(
            '{"Cyl":["-2.25"],"Sph":["-20.00","0.00"]}',
            $client->getResponse()->getContent()
        );
    }

    public function testBothSet(): void
    {
        $client = static::createClient();
        $client->request('GET', '/parameter?Cyl=-2.25&Sph=-20.00');

        $this->assertResponseIsSuccessful();
        $this->assertEquals(
            '{"Cyl":["-2.25"],"Sph":["-20.00"]}',
            $client->getResponse()->getContent()
        );
    }
}
