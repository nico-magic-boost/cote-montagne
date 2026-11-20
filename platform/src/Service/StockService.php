<?php

namespace App\Service;

use App\Entity\Location;
use App\Entity\Product;
use App\Entity\StockLevel;
use App\Entity\StockMovement;
use App\Enum\StockMovementSource;
use App\Enum\StockMovementType;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;

final class StockService
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * Records a movement and updates the level in one transaction.
     * Returns null when the same external event was already recorded (safe re-sync).
     */
    public function record(
        Product $product,
        Location $location,
        int $delta,
        StockMovementType $type,
        StockMovementSource $source,
        ?string $externalId = null,
        ?string $note = null,
        ?\DateTimeImmutable $occurredAt = null,
    ): ?StockMovement {
        if (!$product->isStockManaged()) {
            throw new \LogicException(sprintf('Product "%s" is ordered on demand and has no stock.', $product->getReference()));
        }

        return $this->em->wrapInTransaction(function () use ($product, $location, $delta, $type, $source, $externalId, $note, $occurredAt): ?StockMovement {
            if ($externalId !== null && $this->em->getRepository(StockMovement::class)->findOneBy(['source' => $source, 'externalId' => $externalId])) {
                return null;
            }

            $this->lockedLevel($product, $location)->applyDelta($delta);
            $movement = new StockMovement($product, $location, $delta, $type, $source, $externalId, $note, $occurredAt);
            $this->em->persist($movement);

            return $movement;
        });
    }

    public function transfer(Product $product, Location $from, Location $to, int $units, ?string $note = null): void
    {
        if ($units <= 0) {
            throw new \InvalidArgumentException('A transfer moves at least one unit.');
        }

        $this->em->wrapInTransaction(function () use ($product, $from, $to, $units, $note): void {
            $this->record($product, $from, -$units, StockMovementType::TransferOut, StockMovementSource::Manual, note: $note);
            $this->record($product, $to, $units, StockMovementType::TransferIn, StockMovementSource::Manual, note: $note);
        });
    }

    public function reserve(Product $product, Location $location, int $units): void
    {
        $this->em->wrapInTransaction(fn () => $this->lockedLevel($product, $location)->reserve($units));
    }

    public function release(Product $product, Location $location, int $units): void
    {
        $this->em->wrapInTransaction(fn () => $this->lockedLevel($product, $location)->release($units));
    }

    /** @return array<string, int> available units per location code */
    public function availabilityByLocation(Product $product): array
    {
        $result = [];
        foreach ($this->em->getRepository(StockLevel::class)->findBy(['product' => $product]) as $level) {
            $result[$level->getLocation()->getCode()] = $level->getAvailable();
        }

        return $result;
    }

    private function lockedLevel(Product $product, Location $location): StockLevel
    {
        $level = $this->em->createQueryBuilder()
            ->select('l')->from(StockLevel::class, 'l')
            ->where('l.product = :product AND l.location = :location')
            ->setParameter('product', $product)
            ->setParameter('location', $location)
            ->getQuery()
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->getOneOrNullResult();

        if ($level === null) {
            $level = new StockLevel($product, $location);
            $this->em->persist($level);
        }

        return $level;
    }
}
