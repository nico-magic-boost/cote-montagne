<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[UniqueEntity('slug')]
class Brand
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 120)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(length: 140, unique: true)]
    private string $slug;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $website = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Email]
    private ?string $orderEmail = null;

    #[ORM\Column(nullable: true)]
    #[Assert\PositiveOrZero]
    private ?int $leadTimeDays = null;

    public function __construct(string $name, string $slug)
    {
        $this->name = $name;
        $this->slug = $slug;
    }

    public function getId(): ?int { return $this->id; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): void { $this->slug = $slug; }
    public function getWebsite(): ?string { return $this->website; }
    public function setWebsite(?string $website): void { $this->website = $website; }
    public function getOrderEmail(): ?string { return $this->orderEmail; }
    public function setOrderEmail(?string $orderEmail): void { $this->orderEmail = $orderEmail; }
    public function getLeadTimeDays(): ?int { return $this->leadTimeDays; }
    public function setLeadTimeDays(?int $leadTimeDays): void { $this->leadTimeDays = $leadTimeDays; }
}
