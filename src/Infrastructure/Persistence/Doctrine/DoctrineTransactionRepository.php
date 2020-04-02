<?php

declare(strict_types=1);

namespace PaymentsAPI\Infrastructure\Persistence\Doctrine;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Money\Currency;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\ValueObject\UserId;

/**
 * Class DoctrineTransactionRepository
 * @package PaymentsAPI\Infrastructure\Persistence\Doctrine
 */
class DoctrineTransactionRepository extends ServiceEntityRepository implements TransactionRepository
{
    /**
     * DoctrineTransactionRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    /**
     * @param int|null $limit
     * @return array|Transaction[]
     */
    public function findConfirmed($limit = null): array
    {
        return $this->findBy(['status' => Transaction::STATUS_CONFIRMED], ['createdAt' => 'asc'], $limit);
    }

    /**
     * @return int
     */
    public function getTotalConfirmed(): int
    {
        return $this->count(['status' => Transaction::STATUS_CONFIRMED]);
    }

    /**
     * @param Transaction $transaction
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function add(Transaction $transaction)
    {
        $this->getEntityManager()->persist($transaction);
        $this->getEntityManager()->flush();
    }

    /**
     * @param $userId
     * @param \DateTime $from
     * @param \DateTime $to
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getVolume(UserId $userId, \DateTime $from, \DateTime $to): int
    {
        return (int)$this->createQueryBuilder('transaction')
            ->select('COALESCE(SUM(transaction.amount), 0)')
            ->where('transaction.userId = :userId')
            ->andWhere('transaction.createdAt >= :from')
            ->andWhere('transaction.createdAt <= :to')
            ->setParameter('userId', $userId)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $userId
     * @param Currency $currency
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getUserTransferredAmountForCurrency(UserId $userId, Currency $currency): int
    {
        return (int)$this->createQueryBuilder('transaction')
//            ->select('COALESCE(SUM(transaction.money.amount + transaction.fee.amount), 0)')
            ->select('COALESCE(SUM(transaction.total), 0)')
            ->where('transaction.userId = :userId')
            ->andWhere('transaction.currency.code = :currency')
            ->setParameter('userId', $userId)
            ->setParameter('currency', (string)$currency)
            ->getQuery()->getSingleScalarResult();
    }

    /**
     * @param $userId
     * @param \DateTime $from
     * @param \DateTime $to
     * @return mixed
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function countUserTransactionsInTimePeriod(UserId $userId, \DateTime $from, \DateTime $to): int
    {
        return (int)$this->createQueryBuilder('transaction')
        ->select('COUNT(transaction.id)')
        ->where('transaction.userId = :userId')
        ->andWhere('transaction.createdAt >= :from')
        ->andWhere('transaction.createdAt <= :to')
        ->setParameter('userId', $userId)
        ->setParameter('from', $from)
        ->setParameter('to', $to)
        ->getQuery()->getSingleScalarResult();
    }
}