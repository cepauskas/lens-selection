<?php

declare(strict_types=1);

namespace App\Hydrator;

use Doctrine\ORM\Internal\Hydration\ArrayHydrator;

class AttributeNamesHydrator extends ArrayHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function hydrateAllData(): array
    {
        $data = parent::hydrateAllData();

        return array_map(fn ($row) => $row['name'], $data);
    }
}
