<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class InvoiceLine
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'lines')]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Invoice $invoice;

    #[ORM\Column]
    private int $position;

    #[ORM\Column(length: 255)]
    private string $label;

    #[ORM\Column]
    private int $quantityMilli;

    #[ORM\Column]
    private int $unitPriceHtCents;

    #[ORM\Column]
    private int $vatRateBp;

    #[ORM\Column]
    private int $discountBp;

    #[ORM\Column]
    private int $totalHtCents;

    public function __construct(
        Invoice $invoice,
        int $position,
        string $label,
        int $quantityMilli,
        int $unitPriceHtCents,
        int $vatRateBp,
        int $discountBp,
        int $totalHtCents,
    ) {
        $this->invoice = $invoice;
        $this->position = $position;
        $this->label = $label;
        $this->quantityMilli = $quantityMilli;
        $this->unitPriceHtCents = $unitPriceHtCents;
        $this->vatRateBp = $vatRateBp;
        $this->discountBp = $discountBp;
        $this->totalHtCents = $totalHtCents;
    }

    public function getId(): ?int { return $this->id; }
    public function getInvoice(): Invoice { return $this->invoice; }
    public function getPosition(): int { return $this->position; }
    public function getLabel(): string { return $this->label; }
    public function getQuantityMilli(): int { return $this->quantityMilli; }
    public function getUnitPriceHtCents(): int { return $this->unitPriceHtCents; }
    public function getVatRateBp(): int { return $this->vatRateBp; }
    public function getDiscountBp(): int { return $this->discountBp; }
    public function getTotalHtCents(): int { return $this->totalHtCents; }
}
