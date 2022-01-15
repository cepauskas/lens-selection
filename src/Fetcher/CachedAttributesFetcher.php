<?php

declare(strict_types=1);

namespace App\Fetcher;

use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class CachedAttributesFetcher implements AttributesFetcherInterface
{
    /**
     * @param AttributesFetcherInterface $delegate Delegate to retrieve data from database or some other store
     * @param int $ttl Cache time to live
     * @param CacheInterface $cache Cache implementation
     */
    public function __construct(
        private AttributesFetcherInterface $delegate,
        private int $ttl,
        private CacheInterface $cache
    ) {
    }

    /**
     * Fetches data from the cache, if no hit, fetches data from delegate
     *
     * @inheritDoc
     */
    public function fetch(array $query = []): array
    {
        return $this->cache->get($this->getCacheKey($query), function (ItemInterface $item) use ($query) {
            $item->expiresAfter($this->ttl);
            return $this->delegate->fetch($query);
        });
    }

    /**
     * Creates key for cache storage of from query params
     *
     * @param array $query
     * @return string
     */
    public function getCacheKey(array $query): string
    {
        $keys = array_map(fn ($k, $v) => sprintf('%s.%s', $k, $v), array_keys($query), $query);
        sort($keys);

        $key = implode('.', $keys);
        if (empty($key)) {
            $key = '.';
        }

        return $key;
    }
}
