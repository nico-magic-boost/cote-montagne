<?php

namespace App\Service;

use App\Entity\Quote;
use App\Entity\QuoteLine;

final class QuoteCalculator
{
    public function lineTotalHt(QuoteLine $line): int
    {
        return (int) round(
            $line->getUnitPriceHtCents() * $line->getQuantityMilli() * (10000 - $line->getDiscountBp()) / 10_000_000
        );
    }

    public function lineCost(QuoteLine $line): int
    {
        return (int) round($line->getUnitCostCents() * $line->getQuantityMilli() / 1000);
    }

    public function totals(Quote $quote): QuoteTotals
    {
        $lineTotals = [];
        $htByRate = [];
        $cost = 0;

        foreach ($quote->getLines() as $line) {
            $ht = $this->lineTotalHt($line);
            $lineTotals[] = $ht;
            $htByRate[$line->getVatRateBp()] = ($htByRate[$line->getVatRateBp()] ?? 0) + $ht;
            $cost += $this->lineCost($line);
        }

        // VAT is rounded once per rate, as it appears on the document, not per line.
        $vatByRate = [];
        foreach ($htByRate as $rate => $ht) {
            $vatByRate[$rate] = (int) round($ht * $rate / 10000);
        }
        ksort($vatByRate);

        $totalHt = array_sum($lineTotals);
        $totalVat = array_sum($vatByRate);
        $totalTtc = $totalHt + $totalVat;

        return new QuoteTotals(
            lineTotalsHt: $lineTotals,
            totalHtCents: $totalHt,
            vatByRateBp: $vatByRate,
            totalVatCents: $totalVat,
            totalTtcCents: $totalTtc,
            totalCostCents: $cost,
            marginCents: $totalHt - $cost,
            depositCents: (int) round($totalTtc * $quote->getDepositRateBp() / 10000),
        );
    }
}
