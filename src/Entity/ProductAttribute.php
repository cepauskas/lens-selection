<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: 'App\Repository\ProductAttributeRepository')]
#[ORM\Table(name: 'product_attribute')]
class ProductAttribute
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: 'Product', inversedBy: 'productAttributes')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id')]
    protected Product $product;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: 'Attribute')]
    #[ORM\JoinColumn(name: 'attribute_id', referencedColumnName: 'id')]
    protected Attribute $attribute;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: 'AttributeValue', inversedBy: 'productAttributes')]
    #[ORM\JoinColumn(name: 'attribute_value_id', referencedColumnName: 'id')]
    protected AttributeValue $attributeValue;

    public function setProduct(Product $product): ProductAttribute
    {
        $this->product = $product;

        $product->addProductAttribute($this);

        return $this;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function setAttribute(Attribute $attribute): ProductAttribute
    {
        $this->attribute = $attribute;
        return $this;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }

    public function setAttributeValue(AttributeValue $attributeValue): ProductAttribute
    {
        $this->attributeValue = $attributeValue;
        return $this;
    }

    public function getAttributeValue(): AttributeValue
    {
        return $this->attributeValue;
    }
}
