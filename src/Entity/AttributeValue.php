<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\Repository\AttributeValueRepository')]
#[ORM\Table(name: 'attribute_value')]
#[ORM\Index(name: 'value_idx', fields: ['value'])]
class AttributeValue
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    protected string $value;

    #[ORM\ManyToOne(targetEntity: 'Attribute', inversedBy: 'values')]
    #[ORM\JoinColumn(name: 'attribute_id', referencedColumnName: 'id')]
    protected Attribute $attribute;

    #[ORM\OneToMany(targetEntity: 'ProductAttribute', mappedBy: 'attributeValue', fetch: 'EXTRA_LAZY')]
    protected Collection $productAttributes;

    public function __construct()
    {
        $this->productAttributes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): AttributeValue
    {
        $this->id = $id;
        return $this;
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function setValue(string $value): AttributeValue
    {
        $this->value = $value;
        return $this;
    }

    public function setAttribute(Attribute $attribute): AttributeValue
    {
        $this->attribute = $attribute;
        return $this;
    }

    public function getAttribute(): Attribute
    {
        return $this->attribute;
    }
}
