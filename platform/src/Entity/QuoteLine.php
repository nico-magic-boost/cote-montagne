<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Prices are copied from the product when the line is added, so later catalogue
 * price changes never alter a quote already sent. A line without product is a
 * free line (design fee, labour, delivery…).
 */
#[ORM\Entity]
class QuoteLine
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lines')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Quote $quote;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(onDelete: 'SET NULL')]
    private ?Product $product = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    private string $label;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $description = null;

    /** Thousandths of a unit, so 2.5 m² is 2500. */
    #[ORM\Column]
    #[Assert\Positive]
    private int $quantityMilli = 1000;

    #[ORM\Column]
    private int $unitPriceHtCents;

    #[ORM\Column]
    private int $unitCostCents = 0;

    #[ORM\Column]
    #[Assert\Range(min: 0, max: 10000)]
    private int $vatRateBp = 2000;

    #[ORM\Column]
    #[Assert\Range(min: 0, max: 10000)]
    private int $discountBp = 0;

    #[ORM\Column]
    private int $position = 0;

    public function __construct(Quote $quote, string $label, int $unitPriceHtCents)
    {
        $this->quote = $quote;
        $this->label = $label;
        $this->unitPriceHtCents = $unitPriceHtCents;
    }

    public static function fromProduct(Quote $quote, Product $product, int $quantityMilli = 1000): self
    {
        $line = new self($quote, $product->getBrand()->getName().' — '.$product->getName(), $product->getSalePriceCents());
        $line->product = $product;
        $line->description = $product->getDescription();
        $line->quantityMilli = $quantityMilli;
        $line->unitCostCents = $product->getPurchasePriceCents();
        $line->vatRateBp = $product->getVatRateBp();

        return $line;
    }

    public function getId(): ?int { return $this->id; }
    public function getQuote(): Quote { return $this->quote; }
    public function getProduct(): ?Product { return $this->product; }
    public function getLabel(): string { return $this->label; }
    public function setLabel(string $label): void { $this->label = $label; }
    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): void { $this->description = $description; }
    public function getQuantityMilli(): int { return $this->quantityMilli; }
    public function setQuantityMilli(int $quantityMilli): void { $this->quantityMilli = $quantityMilli; }
    public function getUnitPriceHtCents(): int { return $this->unitPriceHtCents; }
    public function setUnitPriceHtCents(int $cents): void { $this->unitPriceHtCents = $cents; }
    public function getUnitCostCents(): int { return $this->unitCostCents; }
    public function setUnitCostCents(int $cents): void { $this->unitCostCents = $cents; }
    public function getVatRateBp(): int { return $this->vatRateBp; }
    public function setVatRateBp(int $bp): void { $this->vatRateBp = $bp; }
    public function getDiscountBp(): int { return $this->discountBp; }
    public function setDiscountBp(int $bp): void { $this->discountBp = $bp; }
    public function getPosition(): int { return $this->position; }
    public function setPosition(int $position): void { $this->position = $position; }
}
