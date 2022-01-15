<?php

declare(strict_types=1);

namespace App\Tests\Hydrator;

use App\Hydrator\AttributeValuesHydrator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use PHPUnit\Framework\TestCase;

class AttributeValuesHydratorTest extends TestCase
{
    public function testHydrateAllData()
    {
        $platform = $this->createMock(MySQL80Platform::class);

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->any())->method('getDatabasePlatform')->will($this->returnValue($platform));

        $eventManager = $this->createMock(EventManager::class);
        $eventManager->expects($this->atLeastOnce())->method('addEventListener');

        $rsm = $this->createMock(ResultSetMapping::class);
        $rsm->scalarMappings = ['name' => 'name', 'value' => 'value'];
        $rsm->typeMappings = ['name' => 'string', 'value' => 'string'];

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->any())->method('getConnection')->will($this->returnValue($connection));
        $em->expects($this->any())->method('getEventManager')->will($this->returnValue($eventManager));

        $stmt = $this->createMock(Result::class);
        $stmt->expects($this->exactly(6))
            ->method('fetchAssociative')
            ->will($this->onConsecutiveCalls(
                ['name' => 'Sph', 'value' => '-10.25'],
                ['name' => 'Sph', 'value' => '-20.00'],
                ['name' => 'Cyl', 'value' => '-2.25'],
                ['name' => 'Cyl', 'value' => '-1.50'],
                ['name' => 'Cyl', 'value' => '-0.75'],
                null
            ));

        $hydrator = new AttributeValuesHydrator($em);

        return $this->assertEquals(
            ['Sph' => ['-10.25', '-20.00'], 'Cyl' => ['-2.25', '-1.50', '-0.75']],
            $hydrator->hydrateAll($stmt, $rsm)
        );
    }
}
