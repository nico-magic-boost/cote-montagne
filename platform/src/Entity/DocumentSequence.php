<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * One row per (prefix, year). Read under a row lock so two documents can never share a number.
 */
#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'uniq_sequence_prefix_year', columns: ['prefix', 'year'])]
class DocumentSequence
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 5)]
    private string $prefix;

    #[ORM\Column]
    private int $year;

    #[ORM\Column]
    private int $lastValue = 0;

    public function __construct(string $prefix, int $year)
    {
        $this->prefix = $prefix;
        $this->year = $year;
    }

    public function next(): string
    {
        ++$this->lastValue;

        return sprintf('%s-%d-%04d', $this->prefix, $this->year, $this->lastValue);
    }
}
