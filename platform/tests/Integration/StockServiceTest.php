<?php

namespace App\Tests\Integration;

use App\Entity\Brand;
use App\Entity\Location;
use App\Entity\Product;
use App\Entity\StockMovement;
use App\Enum\StockMovementSource;
use App\Enum\StockMovementType;
use App\Service\DocumentNumberer;
use App\Service\StockService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

final class StockServiceTest extends KernelTestCase
{
    private EntityManagerInterface $em;
    private StockService $stock;

    protected function setUp(): void
    {
        self::bootKernel();
        $this->em = self::getContainer()->get(EntityManagerInterface::class);
        $this->stock = self::getContainer()->get(StockService::class);
        $this->em->getConnection()->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->em->getConnection()->rollBack();
        parent::tearDown();
    }

    private function product(bool $stockManaged = true): Product
    {
        $brand = new Brand('Marque', 'marque-'.uniqid());
        $product = new Product($brand, 'REF-'.uniqid(), 'Lampe', 'lampe-'.uniqid());
        $product->setStockManaged($stockManaged);
        $this->em->persist($brand);
        $this->em->persist($product);
        $this->em->flush();

        return $product;
    }

    private function location(string $code): Location
    {
        return $this->em->getRepository(Location::class)->findOneBy(['code' => $code]);
    }

    public function testStockIsTrackedPerLocationAndTransfersBalance(): void
    {
        $product = $this->product();
        $coteMontagne = $this->location('COTE_MONTAGNE');
        $ecrin = $this->location('ECRIN');

        $this->stock->record($product, $coteMontagne, 5, StockMovementType::SupplierReceipt, StockMovementSource::Manual);
        $this->stock->transfer($product, $coteMontagne, $ecrin, 2);
        $this->stock->record($product, $ecrin, -1, StockMovementType::StoreSale, StockMovementSource::Manual);
        $this->stock->reserve($product, $coteMontagne, 1);
        $this->em->flush();

        self::assertSame(['COTE_MONTAGNE' => 2, 'ECRIN' => 1], $this->stock->availabilityByLocation($product));
        self::assertCount(4, $this->em->getRepository(StockMovement::class)->findBy(['product' => $product]));
    }

    public function testSameExternalSaleIsCountedOnce(): void
    {
        $product = $this->product();
        $ecrin = $this->location('ECRIN');

        $first = $this->stock->record($product, $ecrin, -1, StockMovementType::StoreSale, StockMovementSource::Skilou, 'ticket-42');
        $this->em->flush();
        $second = $this->stock->record($product, $ecrin, -1, StockMovementType::StoreSale, StockMovementSource::Skilou, 'ticket-42');
        $this->em->flush();

        self::assertNotNull($first);
        self::assertNull($second);
        self::assertSame(['ECRIN' => -1], $this->stock->availabilityByLocation($product));
    }

    public function testOnDemandProductHasNoStock(): void
    {
        $this->expectException(\LogicException::class);
        $this->stock->record($this->product(false), $this->location('ECRIN'), 1, StockMovementType::SupplierReceipt, StockMovementSource::Manual);
    }

    public function testDocumentNumbersFollowEachOther(): void
    {
        $numberer = self::getContainer()->get(DocumentNumberer::class);
        $date = new \DateTimeImmutable('2031-03-01');

        self::assertSame('F-2031-0001', $numberer->next(DocumentNumberer::INVOICE, $date));
        $this->em->flush();
        self::assertSame('F-2031-0002', $numberer->next(DocumentNumberer::INVOICE, $date));
        self::assertSame('D-2031-0001', $numberer->next(DocumentNumberer::QUOTE, $date));
    }
}
