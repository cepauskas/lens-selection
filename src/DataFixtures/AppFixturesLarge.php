<?php

declare(strict_types=1);

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\FixtureGroupInterface;
use App\Entity\Attribute;
use App\Entity\AttributeValue;
use App\Entity\Product;
use App\Entity\ProductAttribute;

class AppFixturesLarge extends Fixture implements FixtureGroupInterface
{
    public const ATTR_SPH = 'Sph';
    public const ATTR_CYL = 'Cyl';

    public static function getGroups(): array
    {
        return ['large'];
    }

    public function load(ObjectManager $manager): void
    {
        $sph = new Attribute();
        $sph->setName(self::ATTR_SPH);
        $manager->persist($sph);

        $cyl = new Attribute();
        $cyl->setName(self::ATTR_CYL);
        $manager->persist($cyl);

        $attributes = [self::ATTR_SPH => [], self::ATTR_CYL => []];

        // sph
        for ($i = -20; $i <= 20; $i += 0.25) {
            $value = new AttributeValue();
            $value->setValue(sprintf('%01.2f', $i));
            $value->setAttribute($sph);
            $manager->persist($value);
            $attributes[self::ATTR_SPH][] = $value;
        }

        // cyl
        for ($i = -2.25; $i <= -0.75; $i += 0.25) {
            $value = new AttributeValue();
            $value->setValue(sprintf('%01.2f', $i));
            $value->setAttribute($cyl);
            $manager->persist($value);
            $attributes[self::ATTR_CYL][] = $value;
        }

        for ($i = 0; $i < 150; $i++) {
            $product = new Product();
            $product->setName(sprintf('Product %d', $i));
            $manager->persist($product);

            $evenProduct = $i % 2 == 0;

            foreach ($attributes as $attributeKey => $values) {
                $attribute = $attributeKey == self::ATTR_SPH ? $sph : $cyl;

                foreach ($values as $index => $value) {
                    $evenAttribute = $index % 2 == 0;

                    if ($evenAttribute && !$evenProduct) {
                        continue;
                    }

                    if (!$evenAttribute && $evenProduct) {
                        continue;
                    }
                    // even product gets even attributes
                    // non-even product gets non-even attributes

                    $productAttribute = new ProductAttribute();
                    $productAttribute->setProduct($product);
                    $productAttribute->setAttribute($attribute);
                    $productAttribute->setAttributeValue($value);
                    $manager->persist($productAttribute);
                }
            }
        }

        $manager->flush();
    }
}
