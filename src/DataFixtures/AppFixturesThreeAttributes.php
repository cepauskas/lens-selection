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

/**
 * Test data for three attributes lookup.
 * Product4 low | 0,90 | Blue, Green
 * Product5 medium | 0,90 | Blue, Green
 * Product6 high | 90,180 | Blue
 * Axis=90&Color=Green: {"Color":["Green"],"Axis":["90"],"Add":["low","medium"]}
 * Axis=90&Color=Blue: {"Add":["high","low","medium"],"Axis":["90"],"Color":["Blue"]}
 */
class AppFixturesThreeAttributes extends Fixture implements FixtureGroupInterface
{
    public const ATTR_ADD = 'Add';
    public const ATTR_AX = 'Axis';
    public const ATTR_COL = 'Color';

    public static function getGroups(): array
    {
        return ['small'];
    }

    public function load(ObjectManager $manager): void
    {
        $add = new Attribute();
        $add->setName(self::ATTR_ADD);
        $manager->persist($add);

        $addValue1 = new AttributeValue();
        $addValue1->setValue('low');
        $addValue1->setAttribute($add);
        $manager->persist($addValue1);

        $addValue2 = new AttributeValue();
        $addValue2->setValue('medium');
        $addValue2->setAttribute($add);
        $manager->persist($addValue2);

        $addValue3 = new AttributeValue();
        $addValue3->setValue('high');
        $addValue3->setAttribute($add);
        $manager->persist($addValue3);

        $ax = new Attribute();
        $ax->setName(self::ATTR_AX);
        $manager->persist($ax);

        $axValue1 = new AttributeValue();
        $axValue1->setValue('0');
        $axValue1->setAttribute($ax);
        $manager->persist($axValue1);

        $axValue2 = new AttributeValue();
        $axValue2->setValue('90');
        $axValue2->setAttribute($ax);
        $manager->persist($axValue2);

        $axValue3 = new AttributeValue();
        $axValue3->setValue('180');
        $axValue3->setAttribute($ax);
        $manager->persist($axValue3);

        $col = new Attribute();
        $col->setName(self::ATTR_COL);
        $manager->persist($col);

        $colValue1 = new AttributeValue();
        $colValue1->setValue('Blue');
        $colValue1->setAttribute($col);
        $manager->persist($colValue1);

        $colValue2 = new AttributeValue();
        $colValue2->setValue('Green');
        $colValue2->setAttribute($col);
        $manager->persist($colValue2);


        $product = new Product();
        $product->setName('Product4 low | 0,90 | Blue, Green');
        $manager->persist($product);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($add)->setAttributeValue($addValue1);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($ax)->setAttributeValue($axValue1);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($ax)->setAttributeValue($axValue2);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($col)->setAttributeValue($colValue1);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($col)->setAttributeValue($colValue2);
        $manager->persist($productAttribute);

        $product = new Product();
        $product->setName('Product5 medium | 0,90 | Blue, Green');
        $manager->persist($product);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($add)->setAttributeValue($addValue2);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($ax)->setAttributeValue($axValue1);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($ax)->setAttributeValue($axValue2);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($col)->setAttributeValue($colValue1);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($col)->setAttributeValue($colValue2);
        $manager->persist($productAttribute);

        $product = new Product();
        $product->setName('Product6 high | 90,180 | Blue');
        $manager->persist($product);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($add)->setAttributeValue($addValue3);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($ax)->setAttributeValue($axValue2);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($ax)->setAttributeValue($axValue3);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($col)->setAttributeValue($colValue1);
        $manager->persist($productAttribute);

        $manager->flush();
    }
}
