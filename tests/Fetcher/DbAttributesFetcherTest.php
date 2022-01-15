<?php

declare(strict_types=1);

namespace App\Tests\Fetcher;

use App\Repository\AttributeRepository;
use App\Fetcher\DbAttributesFetcher;
use PHPUnit\Framework\TestCase;

class DbAttributesFetcherTest extends TestCase
{
    public function testFetch()
    {
        $repo = $this->createMock(AttributeRepository::class);
        $repo->expects($this->once())->method('getAvailableAttributes')->will($this->returnValue(['Sph', 'Cyl']));
        $repo->expects($this->once())->method('getAvailableAttributesAndValues')->with($this->equalTo(['Cyl' => '1']));

        $fetcher = new DbAttributesFetcher($repo);
        $fetcher->fetch(['Cyl' => '1', 'Col' => 'A']);
    }
}
