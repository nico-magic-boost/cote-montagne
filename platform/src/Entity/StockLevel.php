<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Only changed through StockService, which records a StockMovement for every change.
 */
#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'uniq_stock_product_location', columns: ['product_id', 'location_id'])]
class StockLevel
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false, onDelete: 'CASCADE')]
    private Product $product;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Location $location;

    #[ORM\Column]
    private int $quantity = 0;

    /** Units held for accepted quotes, not yet delivered. */
    #[ORM\Column]
    private int $reserved = 0;

    public function __construct(Product $product, Location $location)
    {
        $this->product = $product;
        $this->location = $location;
    }

    public function getAvailable(): int
    {
        return $this->quantity - $this->reserved;
    }

    public function getId(): ?int { return $this->id; }
    public function getProduct(): Product { return $this->product; }
    public function getLocation(): Location { return $this->location; }
    public function getQuantity(): int { return $this->quantity; }
    public function getReserved(): int { return $this->reserved; }

    public function applyDelta(int $delta): void
    {
        $this->quantity += $delta;
    }

    public function reserve(int $units): void
    {
        $this->reserved += $units;
    }

    public function release(int $units): void
    {
        $this->reserved = max(0, $this->reserved - $units);
    }
}
