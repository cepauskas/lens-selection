<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\Repository\ProductRepository')]
#[ORM\Table(name: 'product')]
class Product
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(type: 'string', length: 255)]
    #[Assert\NotBlank]
    protected string $name;

    #[ORM\OneToMany(targetEntity: 'ProductAttribute', mappedBy: 'product')]
    protected Collection $productAttributes;

    public function __construct()
    {
        $this->productAttributes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Product
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Product
    {
        $this->name = $name;
        return $this;
    }

    public function addProductAttribute(ProductAttribute $productAttribute): Product
    {
        $this->productAttributes->add($productAttribute);
        return $this;
    }

    public function getProductAttribute(): Collection
    {
        return $this->productAttributes;
    }
}
