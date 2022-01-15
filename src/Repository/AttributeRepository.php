<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Attribute;
use App\Entity\AttributeValue;
use App\Entity\ProductAttribute;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

class AttributeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attribute::class);
    }

    /**
     * Returns all attribute names
     *
     * @return array
     */
    public function getAvailableAttributes()
    {
        return $this->createQueryBuilder('a')
            ->select('DISTINCT a.name')
            ->orderBy('a.name', 'ASC')
            ->getQuery()
            ->getResult('AttributeNamesHydrator');
    }

    /**
     * Returns attribute names and values. Applies union filtering, if query is supplied
     *
     * @param array $query key=>value query filter
     * @return array
     */
    public function getAvailableAttributesAndValues(array $query = []): array
    {
        // attributes and atttribute values
        $qb = $this->createQueryBuilder('a')
            ->select('DISTINCT a.name, av.value')
            ->join('a.values', 'av')
            ->join('av.productAttributes', 'pa')
            ->addOrderBy('a.name', 'asc')
            ->addOrderBy('av.value', 'asc');

        $i = 0;
        $params = [];
        foreach ($query as $key => $value) {
            // join to find product IDs which have filtered attribute and filtered value
            // related over product_attribute table

            $paTable = sprintf('pa_%s', $key);
            $aTable = sprintf('a_%s', $key);
            $avTable = sprintf('av_%s', $key);

            // important order of joins: ProductAttribute index usage
            $qb->join(ProductAttribute::class, $paTable, Join::WITH, sprintf('(%s.product = pa.product)', $paTable));
            $qb->join(Attribute::class, $aTable, Join::WITH, sprintf('(%s.id = %s.attribute)', $aTable, $paTable));
            $qb->join(
                AttributeValue::class,
                $avTable,
                Join::WITH,
                sprintf('(%s.id = %s.attributeValue)', $avTable, $paTable)
            );

            $qb->andWhere(sprintf('%s.name = ?%d', $aTable, $i));
            $qb->andWhere(sprintf('%s.value = ?%d', $avTable, $i + 1));

            // exclude other values of filtered attribute
            $qb = $qb->andWhere(sprintf('a.name != ?%d OR (a.name = ?%d AND av.value = ?%d)', $i, $i, $i + 1));

            $params[] = $key;
            $params[] = $value;

            $i += 2;
        }

        return $qb->getQuery()
            ->setParameters($params)
            ->getResult('AttributeValuesHydrator');
    }
}
