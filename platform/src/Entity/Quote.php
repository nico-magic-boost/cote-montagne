<?php

namespace App\Entity;

use App\Enum\QuoteStatus;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Index(name: 'idx_quote_status', columns: ['status'])]
class Quote
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    /** Assigned when the quote is first sent, e.g. D-2026-0001. */
    #[ORM\Column(length: 20, unique: true, nullable: true)]
    private ?string $number = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private Customer $customer;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank]
    private string $title;

    #[ORM\Column(length: 20, enumType: QuoteStatus::class)]
    private QuoteStatus $status = QuoteStatus::Draft;

    /** Deposit asked on acceptance, in basis points of the TTC total (3000 = 30 %). */
    #[ORM\Column]
    #[Assert\Range(min: 0, max: 10000)]
    private int $depositRateBp = 0;

    #[ORM\Column(type: 'date_immutable', nullable: true)]
    private ?\DateTimeImmutable $validUntil = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    /** @var Collection<int, QuoteLine> */
    #[ORM\OneToMany(targetEntity: QuoteLine::class, mappedBy: 'quote', cascade: ['persist'], orphanRemoval: true)]
    #[ORM\OrderBy(['position' => 'ASC'])]
    private Collection $lines;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $sentAt = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $answeredAt = null;

    public function __construct(Customer $customer, string $title)
    {
        $this->customer = $customer;
        $this->title = $title;
        $this->lines = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function addLine(QuoteLine $line): void
    {
        $line->setPosition($this->lines->count());
        $this->lines->add($line);
    }

    public function removeLine(QuoteLine $line): void
    {
        $this->lines->removeElement($line);
    }

    public function markSent(string $number, \DateTimeImmutable $at): void
    {
        $this->number ??= $number;
        $this->status = QuoteStatus::Sent;
        $this->sentAt = $at;
    }

    public function markAnswered(bool $accepted, \DateTimeImmutable $at): void
    {
        $this->status = $accepted ? QuoteStatus::Accepted : QuoteStatus::Refused;
        $this->answeredAt = $at;
    }

    public function getId(): ?int { return $this->id; }
    public function getNumber(): ?string { return $this->number; }
    public function getCustomer(): Customer { return $this->customer; }
    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): void { $this->title = $title; }
    public function getStatus(): QuoteStatus { return $this->status; }
    public function getDepositRateBp(): int { return $this->depositRateBp; }
    public function setDepositRateBp(int $bp): void { $this->depositRateBp = $bp; }
    public function getValidUntil(): ?\DateTimeImmutable { return $this->validUntil; }
    public function setValidUntil(?\DateTimeImmutable $date): void { $this->validUntil = $date; }
    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): void { $this->notes = $notes; }
    /** @return Collection<int, QuoteLine> */
    public function getLines(): Collection { return $this->lines; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getSentAt(): ?\DateTimeImmutable { return $this->sentAt; }
    public function getAnsweredAt(): ?\DateTimeImmutable { return $this->answeredAt; }
}
