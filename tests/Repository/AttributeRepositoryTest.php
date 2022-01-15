<?php

declare(strict_types=1);

namespace App\Tests\Repository;

use App\Entity\Attribute;
use Doctrine\ORM\EntityManager;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class AttributeRepositoryTest extends KernelTestCase
{
    private ?EntityManager $entityManager;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();

        $this->entityManager = $kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testGetAvailableAttributes()
    {
        $attribtues = $this->entityManager
            ->getRepository(Attribute::class)
            ->getAvailableAttributes();

        $this->assertEquals(['Add', 'Axis', 'Color', 'Cyl', 'Sph'], $attribtues);
    }

    public function testGetAvailableAttributesAndValuesWithoutQueryParams()
    {
        $attribtues = $this->entityManager
            ->getRepository(Attribute::class)
            ->getAvailableAttributesAndValues();

        $this->assertEquals(
            [
                'Add' => ['high','low','medium'],
                'Axis' => ['0','180','90'],
                'Color' => ['Blue','Green'],
                'Cyl' => ['-0.75', '-1.50', '-2.25'],
                'Sph' => ['-20.00', '+20.00', '0.00']
            ],
            $attribtues
        );
    }

    public function testGetAvailableAttributesAndValuesWithQueryParams()
    {
        $attribtues = $this->entityManager
            ->getRepository(Attribute::class)
            ->getAvailableAttributesAndValues(['Cyl' => '-0.75']);

        $this->assertEquals(
            [
                'Cyl' => ['-0.75'],
                'Sph' => ['+20.00']
            ],
            $attribtues
        );
    }

    public function testGetAvailableAttributesAndValuesWithQueryParams2()
    {
        $attribtues = $this->entityManager
            ->getRepository(Attribute::class)
            ->getAvailableAttributesAndValues(['Sph' => '+20.00']);

        $this->assertEquals(
            [
                'Cyl' => ['-0.75', '-1.50'],
                'Sph' => ['+20.00']
            ],
            $attribtues
        );
    }

    public function testGetAvailableAttributesAndValuesWithQueryParams3()
    {
        $attribtues = $this->entityManager
            ->getRepository(Attribute::class)
            ->getAvailableAttributesAndValues(['Axis' => '90', 'Color' => 'Green']);

        $this->assertEquals(
            [
                'Add' => ['low', 'medium'],
                'Axis' => ['90'],
                'Color' => ['Green'],
            ],
            $attribtues
        );
    }

    public function testGetAvailableAttributesAndValuesWithQueryParams4()
    {
        $attribtues = $this->entityManager
            ->getRepository(Attribute::class)
            ->getAvailableAttributesAndValues(['Axis' => '90', 'Color' => 'Blue']);

        $this->assertEquals(
            [
                'Add' => ['high', 'low', 'medium'],
                'Axis' => ['90'],
                'Color' => ['Blue'],
            ],
            $attribtues
        );
    }

    public function testGetAvailableAttributesAndValuesWithQueryParams5()
    {
        $attribtues = $this->entityManager
            ->getRepository(Attribute::class)
            ->getAvailableAttributesAndValues(['Color' => 'Blue']);

        $this->assertEquals(
            [
                'Add' => ['high', 'low', 'medium'],
                'Axis' => ['0', '180', '90'],
                'Color' => ['Blue'],
            ],
            $attribtues
        );
    }

    public function testGetAvailableAttributesAndValuesWithQueryParams6()
    {
        $attribtues = $this->entityManager
            ->getRepository(Attribute::class)
            ->getAvailableAttributesAndValues(['Color' => 'Green']);

        $this->assertEquals(
            [
                'Add' => ['low', 'medium'],
                'Axis' => ['0', '90'],
                'Color' => ['Green'],
            ],
            $attribtues
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // doing this is recommended to avoid memory leaks
        $this->entityManager->close();
        $this->entityManager = null;
    }
}
