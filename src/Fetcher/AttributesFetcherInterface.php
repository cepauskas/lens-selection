<?php

declare(strict_types=1);

namespace App\Fetcher;

interface AttributesFetcherInterface
{
    /**
     * Returns data for the REST API endpoint
     *
     * @param array $query [key => value] query params, e.g. Sph => -20.00, Cyl => -0.75
     * @return array Multidimentional array containing key => values dataset
     */
    public function fetch(array $query = []): array;
}
