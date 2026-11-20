<?php

namespace App\Entity;

use App\Enum\InvoiceType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * An issued invoice is never edited: corrections go through a credit note.
 * Customer details and lines are copied at issue time for the same reason.
 */
#[ORM\Entity]
class Invoice
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    private string $number;

    #[ORM\Column(length: 20, enumType: InvoiceType::class)]
    private InvoiceType $type;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Customer $customer;

    #[ORM\ManyToOne]
    private ?Quote $quote = null;

    #[ORM\ManyToOne]
    private ?Invoice $creditedInvoice = null;

    #[ORM\Column(length: 200)]
    private string $billingName;

    #[ORM\Column(type: 'text')]
    private string $billingAddress;

    #[ORM\Column]
    private int $totalHtCents;

    #[ORM\Column]
    private int $totalVatCents;

    #[ORM\Column]
    private int $totalTtcCents;

    /** @var Collection<int, InvoiceLine> */
    #[ORM\OneToMany(targetEntity: InvoiceLine::class, mappedBy: 'invoice', cascade: ['persist'])]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $lines;

    /** @var Collection<int, Payment> */
    #[ORM\OneToMany(targetEntity: Payment::class, mappedBy: 'invoice')]
    private Collection $payments;

    #[ORM\Column]
    private \DateTimeImmutable $issuedAt;

    #[ORM\Column(type: 'date_immutable')]
    private \DateTimeImmutable $dueAt;

    /**
     * @param list<array{label: string, quantityMilli: int, unitPriceHtCents: int, vatRateBp: int, discountBp: int, totalHtCents: int}> $lines
     */
    public function __construct(
        string $number,
        InvoiceType $type,
        Customer $customer,
        array $lines,
        int $totalHtCents,
        int $totalVatCents,
        \DateTimeImmutable $issuedAt,
        \DateTimeImmutable $dueAt,
        ?Quote $quote = null,
    ) {
        $this->number = $number;
        $this->type = $type;
        $this->customer = $customer;
        $this->quote = $quote;
        $this->billingName = $customer->getDisplayName();
        $this->billingAddress = $customer->getPostalAddress();
        $this->totalHtCents = $totalHtCents;
        $this->totalVatCents = $totalVatCents;
        $this->totalTtcCents = $totalHtCents + $totalVatCents;
        $this->issuedAt = $issuedAt;
        $this->dueAt = $dueAt;
        $this->lines = new ArrayCollection();
        $this->payments = new ArrayCollection();

        foreach (array_values($lines) as $position => $line) {
            $this->lines->add(new InvoiceLine($this, $position, ...$line));
        }
    }

    public function setCreditedInvoice(Invoice $invoice): void
    {
        $this->creditedInvoice = $invoice;
    }

    public function getPaidCents(): int
    {
        $paid = 0;
        foreach ($this->payments as $payment) {
            $paid += $payment->getAmountCents();
        }

        return $paid;
    }

    public function getBalanceDueCents(): int
    {
        return $this->totalTtcCents - $this->getPaidCents();
    }

    public function isPaid(): bool
    {
        return $this->getBalanceDueCents() <= 0;
    }

    public function getId(): ?int { return $this->id; }
    public function getNumber(): string { return $this->number; }
    public function getType(): InvoiceType { return $this->type; }
    public function getCustomer(): Customer { return $this->customer; }
    public function getQuote(): ?Quote { return $this->quote; }
    public function getCreditedInvoice(): ?Invoice { return $this->creditedInvoice; }
    public function getBillingName(): string { return $this->billingName; }
    public function getBillingAddress(): string { return $this->billingAddress; }
    public function getTotalHtCents(): int { return $this->totalHtCents; }
    public function getTotalVatCents(): int { return $this->totalVatCents; }
    public function getTotalTtcCents(): int { return $this->totalTtcCents; }
    /** @return Collection<int, InvoiceLine> */
    public function getLines(): Collection { return $this->lines; }
    /** @return Collection<int, Payment> */
    public function getPayments(): Collection { return $this->payments; }
    public function getIssuedAt(): \DateTimeImmutable { return $this->issuedAt; }
    public function getDueAt(): \DateTimeImmutable { return $this->dueAt; }
}
