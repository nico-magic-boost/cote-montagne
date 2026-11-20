<?php

namespace App\Tests\Unit;

use App\Entity\Brand;
use App\Entity\Customer;
use App\Entity\Product;
use App\Entity\Quote;
use App\Entity\QuoteLine;
use App\Service\QuoteCalculator;
use PHPUnit\Framework\TestCase;

final class QuoteCalculatorTest extends TestCase
{
    private function quote(): Quote
    {
        return new Quote(new Customer('Dupont'), 'Séjour chalet');
    }

    public function testTotalsWithMixedVatRatesDiscountAndMargin(): void
    {
        $quote = $this->quote();
        $quote->setDepositRateBp(3000);

        $product = new Product(new Brand('Maison Test', 'maison-test'), 'CAN-01', 'Canapé laine', 'canape-laine');
        $product->setPurchasePriceCents(100000);
        $product->setSalePriceCents(200000);

        $sofa = QuoteLine::fromProduct($quote, $product, 2000);
        $sofa->setDiscountBp(1000);
        $quote->addLine($sofa);

        $fee = new QuoteLine($quote, 'Pose et mise en place', 50000);
        $fee->setVatRateBp(1000);
        $quote->addLine($fee);

        $totals = (new QuoteCalculator())->totals($quote);

        self::assertSame([360000, 50000], $totals->lineTotalsHt);
        self::assertSame(410000, $totals->totalHtCents);
        self::assertSame([1000 => 5000, 2000 => 72000], $totals->vatByRateBp);
        self::assertSame(77000, $totals->totalVatCents);
        self::assertSame(487000, $totals->totalTtcCents);
        self::assertSame(200000, $totals->totalCostCents);
        self::assertSame(210000, $totals->marginCents);
        self::assertSame(5122, $totals->marginRateBp());
        self::assertSame(146100, $totals->depositCents);
        self::assertSame('Maison Test — Canapé laine', $sofa->getLabel());
    }

    public function testFractionalQuantityIsRounded(): void
    {
        $quote = $this->quote();
        $line = new QuoteLine($quote, 'Parquet chêne (m²)', 4999);
        $line->setQuantityMilli(2333);
        $quote->addLine($line);

        $totals = (new QuoteCalculator())->totals($quote);

        self::assertSame(11663, $totals->totalHtCents);
        self::assertSame(2333, $totals->totalVatCents);
    }

    public function testEmptyQuote(): void
    {
        $totals = (new QuoteCalculator())->totals($this->quote());

        self::assertSame(0, $totals->totalTtcCents);
        self::assertSame(0, $totals->marginRateBp());
    }
}
