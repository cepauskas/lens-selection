<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: 'App\Repository\AttributeRepository')]
#[ORM\Table(name: 'attribute')]
#[ORM\Index(name: 'name_idx', fields: ['name'])]
class Attribute
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    protected ?int $id;

    #[ORM\Column(type: 'string', length: 63)]
    #[Assert\NotBlank]
    protected ?string $name = null;

    #[ORM\OneToMany(targetEntity: 'AttributeValue', mappedBy: 'attribute')]
    protected Collection $values;

    public function __construct()
    {
        $this->values = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Attribute
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Attribute
    {
        $this->name = $name;
        return $this;
    }

    public function addValue(AttributeValue $value): Attribute
    {
        $this->values->add($value);
        return $this;
    }

    public function getValues(): Collection
    {
        return $this->values;
    }
}
