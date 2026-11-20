<?php

namespace App\Service;

final readonly class QuoteTotals
{
    /**
     * @param array<int, int> $vatByRateBp     VAT amount in cents, keyed by rate in basis points
     * @param array<int, int> $lineTotalsHt    HT total per line, in line order
     */
    public function __construct(
        public array $lineTotalsHt,
        public int $totalHtCents,
        public array $vatByRateBp,
        public int $totalVatCents,
        public int $totalTtcCents,
        public int $totalCostCents,
        public int $marginCents,
        public int $depositCents,
    ) {
    }

    /** Margin as a share of the HT total, in basis points (4000 = 40 %). */
    public function marginRateBp(): int
    {
        return $this->totalHtCents === 0 ? 0 : (int) round($this->marginCents * 10000 / $this->totalHtCents);
    }
}
