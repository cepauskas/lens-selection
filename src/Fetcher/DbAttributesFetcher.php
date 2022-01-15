<?php

declare(strict_types=1);

namespace App\Fetcher;

use App\Repository\AttributeRepository;

class DbAttributesFetcher implements AttributesFetcherInterface
{
    public function __construct(private AttributeRepository $attributeRepository)
    {
    }

    /**
     * Fetches data from the database
     *
     * @inheritDoc
     */
    public function fetch(array $query = []): array
    {
        // filter out non valid attribute names
        $availableKeys = $this->attributeRepository->getAvailableAttributes();
        $query = array_intersect_key($query, array_flip($availableKeys));

        // query and return
        return $this->attributeRepository->getAvailableAttributesAndValues($query);
    }
}
