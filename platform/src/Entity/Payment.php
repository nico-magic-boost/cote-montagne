<?php

namespace App\Entity;

use App\Enum\PaymentMethod;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'uniq_payment_external', columns: ['external_ref'])]
class Payment
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'payments')]
    #[ORM\JoinColumn(nullable: false)]
    private Invoice $invoice;

    #[ORM\Column]
    private int $amountCents;

    #[ORM\Column(length: 20, enumType: PaymentMethod::class)]
    private PaymentMethod $method;

    /** e.g. the Stripe payment id, so a webhook received twice is recorded once. */
    #[ORM\Column(length: 120, nullable: true)]
    private ?string $externalRef = null;

    #[ORM\Column]
    private \DateTimeImmutable $paidAt;

    public function __construct(Invoice $invoice, int $amountCents, PaymentMethod $method, \DateTimeImmutable $paidAt, ?string $externalRef = null)
    {
        $this->invoice = $invoice;
        $this->amountCents = $amountCents;
        $this->method = $method;
        $this->paidAt = $paidAt;
        $this->externalRef = $externalRef;
        $invoice->getPayments()->add($this);
    }

    public function getId(): ?int { return $this->id; }
    public function getInvoice(): Invoice { return $this->invoice; }
    public function getAmountCents(): int { return $this->amountCents; }
    public function getMethod(): PaymentMethod { return $this->method; }
    public function getExternalRef(): ?string { return $this->externalRef; }
    public function getPaidAt(): \DateTimeImmutable { return $this->paidAt; }
}
