<?php

declare(strict_types=1);

namespace App\Tests\Fetcher;

use App\Fetcher\CachedAttributesFetcher;
use App\Fetcher\DbAttributesFetcher;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CachedAttributesFetcherTest extends KernelTestCase
{
    private ?CacheInterface $cache;

    protected function setUp(): void
    {
        $kernel = self::bootKernel();
        $this->cache = $kernel->getContainer()->get('cache.app');
    }

    public function testFetchNotInCache()
    {
        $this->cache->delete('Test.1');

        $data = ['Test' => ['2.00']];
        $delegate = $this->createMock(DbAttributesFetcher::class);
        $delegate->expects($this->once())->method('fetch')->will($this->returnValue($data));

        $fetcher = new CachedAttributesFetcher($delegate, 3600, $this->cache);
        $this->assertEquals($data, $fetcher->fetch(['Test' => '1']));
    }

    public function testFetchInCache()
    {
        $this->cache->get('Test.2', function (ItemInterface $item) {
            $item->expiresAfter(3600);
            return ['Test' => ['From', 'Cache']];
        });

        $delegate = $this->createMock(DbAttributesFetcher::class);
        $delegate->expects($this->never())->method('fetch');

        $fetcher = new CachedAttributesFetcher($delegate, 3600, $this->cache);
        $this->assertEquals(['Test' => ['From', 'Cache']], $fetcher->fetch(['Test' => '2']));
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        $this->cache->delete('Test.1');
        $this->cache->delete('Test.2');
    }
}
