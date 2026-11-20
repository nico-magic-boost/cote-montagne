<?php

namespace App\Entity;

use App\Enum\StockMovementSource;
use App\Enum\StockMovementType;
use Doctrine\ORM\Mapping as ORM;

/**
 * Append-only journal: a stock level must always equal the sum of its movements.
 */
#[ORM\Entity]
#[ORM\Index(name: 'idx_movement_product_location', columns: ['product_id', 'location_id'])]
#[ORM\UniqueConstraint(name: 'uniq_movement_external', columns: ['source', 'external_id'])]
class StockMovement
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
    private int $delta;

    #[ORM\Column(length: 30, enumType: StockMovementType::class)]
    private StockMovementType $type;

    #[ORM\Column(length: 20, enumType: StockMovementSource::class)]
    private StockMovementSource $source;

    /** Id of the event in the source system (e.g. a Skilou sale), so a re-sync never counts it twice. */
    #[ORM\Column(length: 120, nullable: true)]
    private ?string $externalId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $note = null;

    #[ORM\Column]
    private \DateTimeImmutable $occurredAt;

    public function __construct(
        Product $product,
        Location $location,
        int $delta,
        StockMovementType $type,
        StockMovementSource $source,
        ?string $externalId = null,
        ?string $note = null,
        ?\DateTimeImmutable $occurredAt = null,
    ) {
        $this->product = $product;
        $this->location = $location;
        $this->delta = $delta;
        $this->type = $type;
        $this->source = $source;
        $this->externalId = $externalId;
        $this->note = $note;
        $this->occurredAt = $occurredAt ?? new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }
    public function getProduct(): Product { return $this->product; }
    public function getLocation(): Location { return $this->location; }
    public function getDelta(): int { return $this->delta; }
    public function getType(): StockMovementType { return $this->type; }
    public function getSource(): StockMovementSource { return $this->source; }
    public function getExternalId(): ?string { return $this->externalId; }
    public function getNote(): ?string { return $this->note; }
    public function getOccurredAt(): \DateTimeImmutable { return $this->occurredAt; }
}
