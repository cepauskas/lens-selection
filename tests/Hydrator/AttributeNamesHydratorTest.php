<?php

declare(strict_types=1);

namespace App\Tests\Hydrator;

use App\Hydrator\AttributeNamesHydrator;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\Common\EventManager;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\DBAL\Platforms\MySQL80Platform;
use PHPUnit\Framework\TestCase;

class AttributeNamesHydratorTest extends TestCase
{
    public function testHydrateAllData()
    {
        $platform = $this->createMock(MySQL80Platform::class);

        $connection = $this->createMock(Connection::class);
        $connection->expects($this->any())->method('getDatabasePlatform')->will($this->returnValue($platform));

        $eventManager = $this->createMock(EventManager::class);
        $eventManager->expects($this->atLeastOnce())->method('addEventListener');

        $rsm = $this->createMock(ResultSetMapping::class);
        $rsm->scalarMappings = ['name' => 'name'];
        $rsm->typeMappings = ['name' => 'string'];

        $em = $this->createMock(EntityManagerInterface::class);
        $em->expects($this->any())->method('getConnection')->will($this->returnValue($connection));
        $em->expects($this->any())->method('getEventManager')->will($this->returnValue($eventManager));

        $stmt = $this->createMock(Result::class);
        $stmt->expects($this->exactly(3))
            ->method('fetchAssociative')
            ->will($this->onConsecutiveCalls(['name' => 'a'], ['name' => 'b'], null));

        $hydrator = new AttributeNamesHydrator($em);

        return $this->assertEquals(['a', 'b'], $hydrator->hydrateAll($stmt, $rsm));
    }
}
