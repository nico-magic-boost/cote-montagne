<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Prices are integers in cents, HT. VAT rate is in basis points (2000 = 20 %).
 */
#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'uniq_product_brand_reference', columns: ['brand_id', 'reference'])]
#[ORM\Index(name: 'idx_product_name', columns: ['name'])]
#[UniqueEntity('slug')]
class Product
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Brand $brand;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Category $category = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    private string $reference;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank]
    private string $name;

    #[ORM\Column(length: 220, unique: true)]
    private string $slug;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $dimensions = null;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $color = null;

    #[ORM\Column(length: 120, nullable: true)]
    private ?string $material = null;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private int $purchasePriceCents = 0;

    #[ORM\Column]
    #[Assert\PositiveOrZero]
    private int $salePriceCents = 0;

    #[ORM\Column]
    #[Assert\Range(min: 0, max: 10000)]
    private int $vatRateBp = 2000;

    /** False = always ordered from the supplier, never held in stock. */
    #[ORM\Column]
    private bool $stockManaged = true;

    #[ORM\Column]
    private bool $publishedOnline = false;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $skilouRef = null;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct(Brand $brand, string $reference, string $name, string $slug)
    {
        $this->brand = $brand;
        $this->reference = $reference;
        $this->name = $name;
        $this->slug = $slug;
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getMarginCents(): int
    {
        return $this->salePriceCents - $this->purchasePriceCents;
    }

    public function getId(): ?int { return $this->id; }
    public function getBrand(): Brand { return $this->brand; }
    public function setBrand(Brand $brand): void { $this->brand = $brand; }
    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $category): void { $this->category = $category; }
    public function getReference(): string { return $this->reference; }
    public function setReference(string $reference): void { $this->reference = $reference; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): void { $this->slug = $slug; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function getDimensions(): ?string { return $this->dimensions; }
    public function setDimensions(?string $dimensions): void { $this->dimensions = $dimensions; }
    public function getColor(): ?string { return $this->color; }
    public function setColor(?string $color): void { $this->color = $color; }
    public function getMaterial(): ?string { return $this->material; }
    public function setMaterial(?string $material): void { $this->material = $material; }
    public function getPurchasePriceCents(): int { return $this->purchasePriceCents; }
    public function setPurchasePriceCents(int $cents): void { $this->purchasePriceCents = $cents; $this->touch(); }
    public function getSalePriceCents(): int { return $this->salePriceCents; }
    public function setSalePriceCents(int $cents): void { $this->salePriceCents = $cents; $this->touch(); }
    public function getVatRateBp(): int { return $this->vatRateBp; }
    public function setVatRateBp(int $bp): void { $this->vatRateBp = $bp; }
    public function isStockManaged(): bool { return $this->stockManaged; }
    public function setStockManaged(bool $managed): void { $this->stockManaged = $managed; }
    public function isPublishedOnline(): bool { return $this->publishedOnline; }
    public function setPublishedOnline(bool $published): void { $this->publishedOnline = $published; }
    public function getSkilouRef(): ?string { return $this->skilouRef; }
    public function setSkilouRef(?string $ref): void { $this->skilouRef = $ref; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }

    private function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }
}
