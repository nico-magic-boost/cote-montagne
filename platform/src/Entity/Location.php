<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
class Location
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 40, unique: true)]
    private string $code;

    #[ORM\Column(length: 120)]
    private string $name;

    /** Whether online orders may be fulfilled from this location. */
    #[ORM\Column]
    private bool $fulfilsOnlineOrders = false;

    #[ORM\Column(length: 80, nullable: true)]
    private ?string $skilouRef = null;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function getId(): ?int { return $this->id; }
    public function getCode(): string { return $this->code; }
    public function getName(): string { return $this->name; }
    public function setName(string $name): void { $this->name = $name; }
    public function fulfilsOnlineOrders(): bool { return $this->fulfilsOnlineOrders; }
    public function setFulfilsOnlineOrders(bool $value): void { $this->fulfilsOnlineOrders = $value; }
    public function getSkilouRef(): ?string { return $this->skilouRef; }
    public function setSkilouRef(?string $ref): void { $this->skilouRef = $ref; }
}
