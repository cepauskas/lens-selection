<?php

declare(strict_types=1);

namespace App\Hydrator;

use Doctrine\ORM\Internal\Hydration\ArrayHydrator;

class AttributeValuesHydrator extends ArrayHydrator
{
    /**
     * {@inheritdoc}
     */
    protected function hydrateAllData(): array
    {
        $data = parent::hydrateAllData();

        $remap = [];
        foreach ($data as $row) {
            if (!isset($remap[$row['name']])) {
                $remap[$row['name']] = [];
            }

            $remap[$row['name']][] = $row['value'];
        }
        return $remap;
    }
}
