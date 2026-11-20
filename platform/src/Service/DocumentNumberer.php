<?php

namespace App\Service;

use App\Entity\DocumentSequence;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Must be called inside the transaction that saves the document, so a number is
 * only consumed when the document really exists: invoices must have no gaps.
 */
final class DocumentNumberer
{
    public const QUOTE = 'D';
    public const INVOICE = 'F';
    public const CREDIT_NOTE = 'AV';

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function next(string $prefix, \DateTimeImmutable $date): string
    {
        if (!$this->em->getConnection()->isTransactionActive()) {
            throw new \LogicException('Document numbers must be taken inside a transaction.');
        }

        $year = (int) $date->format('Y');
        $sequence = $this->em->createQueryBuilder()
            ->select('s')->from(DocumentSequence::class, 's')
            ->where('s.prefix = :prefix AND s.year = :year')
            ->setParameter('prefix', $prefix)
            ->setParameter('year', $year)
            ->getQuery()
            ->setLockMode(LockMode::PESSIMISTIC_WRITE)
            ->getOneOrNullResult();

        if ($sequence === null) {
            $sequence = new DocumentSequence($prefix, $year);
            $this->em->persist($sequence);
        }

        return $sequence->next();
    }
}
