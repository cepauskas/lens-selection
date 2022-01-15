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

class AppFixtures extends Fixture implements FixtureGroupInterface
{
    public const ATTR_SPH = 'Sph';
    public const ATTR_CYL = 'Cyl';

    public static function getGroups(): array
    {
        return ['small', 'mini'];
    }

    public function load(ObjectManager $manager): void
    {
        $sph = new Attribute();
        $sph->setName(self::ATTR_SPH);
        $manager->persist($sph);

        $sphValue1 = new AttributeValue();
        $sphValue1->setValue('-20.00');
        $sphValue1->setAttribute($sph);
        $manager->persist($sphValue1);

        $sphValue2 = new AttributeValue();
        $sphValue2->setValue('0.00');
        $sphValue2->setAttribute($sph);
        $manager->persist($sphValue2);

        $sphValue3 = new AttributeValue();
        $sphValue3->setValue('+20.00');
        $sphValue3->setAttribute($sph);
        $manager->persist($sphValue3);

        $cyl = new Attribute();
        $cyl->setName(self::ATTR_CYL);
        $manager->persist($cyl);

        $cylValue1 = new AttributeValue();
        $cylValue1->setValue('-2.25');
        $cylValue1->setAttribute($cyl);
        $manager->persist($cylValue1);

        $cylValue2 = new AttributeValue();
        $cylValue2->setValue('-1.50');
        $cylValue2->setAttribute($cyl);
        $manager->persist($cylValue2);

        $cylValue3 = new AttributeValue();
        $cylValue3->setValue('-0.75');
        $cylValue3->setAttribute($cyl);
        $manager->persist($cylValue3);

        $product = new Product();
        $product->setName('Product1 -20.00 | -2.25,-1.50');
        $manager->persist($product);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($sph)->setAttributeValue($sphValue1);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($cyl)->setAttributeValue($cylValue1);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($cyl)->setAttributeValue($cylValue2);
        $manager->persist($productAttribute);

        $product = new Product();
        $product->setName('Product2 0.00 | -2.25,-1.50');
        $manager->persist($product);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($sph)->setAttributeValue($sphValue2);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($cyl)->setAttributeValue($cylValue1);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($cyl)->setAttributeValue($cylValue2);
        $manager->persist($productAttribute);

        $product = new Product();
        $product->setName('Product3 +20.00 | -1.50,-0.75');
        $manager->persist($product);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($sph)->setAttributeValue($sphValue3);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($cyl)->setAttributeValue($cylValue2);
        $manager->persist($productAttribute);

        $productAttribute = new ProductAttribute();
        $productAttribute->setProduct($product)->setAttribute($cyl)->setAttributeValue($cylValue3);
        $manager->persist($productAttribute);

        $manager->flush();
    }
}
