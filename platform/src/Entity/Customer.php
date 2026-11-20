<?php

namespace App\Entity;

use App\Enum\CustomerType;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity]
#[ORM\Index(name: 'idx_customer_last_name', columns: ['last_name'])]
#[ORM\Index(name: 'idx_customer_email', columns: ['email'])]
class Customer
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, enumType: CustomerType::class)]
    private CustomerType $type = CustomerType::Individual;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $firstName = null;

    #[ORM\Column(length: 80)]
    #[Assert\NotBlank]
    private string $lastName;

    #[ORM\Column(length: 160, nullable: true)]
    private ?string $companyName = null;

    #[ORM\Column(length: 180, nullable: true)]
    #[Assert\Email]
    private ?string $email = null;

    #[ORM\Column(length: 30, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $street = null;

    #[ORM\Column(length: 12, nullable: true)]
    private ?string $postalCode = null;

    #[ORM\Column(length: 100, nullable: true)]
    private ?string $city = null;

    #[ORM\Column(length: 2)]
    private string $country = 'FR';

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $notes = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    public function __construct(string $lastName)
    {
        $this->lastName = $lastName;
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getDisplayName(): string
    {
        if ($this->type === CustomerType::Company && $this->companyName) {
            return $this->companyName;
        }

        return trim(($this->firstName ?? '').' '.$this->lastName);
    }

    public function getPostalAddress(): string
    {
        return trim(implode("\n", array_filter([
            $this->street,
            trim(($this->postalCode ?? '').' '.($this->city ?? '')),
        ])));
    }

    public function getId(): ?int { return $this->id; }
    public function getType(): CustomerType { return $this->type; }
    public function setType(CustomerType $type): void { $this->type = $type; }
    public function getFirstName(): ?string { return $this->firstName; }
    public function setFirstName(?string $firstName): void { $this->firstName = $firstName; }
    public function getLastName(): string { return $this->lastName; }
    public function setLastName(string $lastName): void { $this->lastName = $lastName; }
    public function getCompanyName(): ?string { return $this->companyName; }
    public function setCompanyName(?string $companyName): void { $this->companyName = $companyName; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(?string $email): void { $this->email = $email; }
    public function getPhone(): ?string { return $this->phone; }
    public function setPhone(?string $phone): void { $this->phone = $phone; }
    public function getStreet(): ?string { return $this->street; }
    public function setStreet(?string $street): void { $this->street = $street; }
    public function getPostalCode(): ?string { return $this->postalCode; }
    public function setPostalCode(?string $postalCode): void { $this->postalCode = $postalCode; }
    public function getCity(): ?string { return $this->city; }
    public function setCity(?string $city): void { $this->city = $city; }
    public function getCountry(): string { return $this->country; }
    public function setCountry(string $country): void { $this->country = $country; }
    public function getNotes(): ?string { return $this->notes; }
    public function setNotes(?string $notes): void { $this->notes = $notes; }
    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
}
