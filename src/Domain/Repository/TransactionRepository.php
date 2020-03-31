<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Repository;

use Money\Currency;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\ValueObject\UserId;

/**
 * Interface TransactionRepository
 * @package PaymentsAPI\Domain\Repository
 */
interface TransactionRepository
{
    /**
     * @param $id
     * @return Transaction
     */
    public function find($id);

    /**
     * @param null $limit
     * @return array|Transaction[]
     */
    public function findConfirmed($limit = null): array;

    /**
     * @return int
     */
    public function getTotalConfirmed(): int;

    /**
     * @param Transaction $transaction
     * @return void
     */
    public function add(Transaction $transaction);

    /**
     * @param $userId
     * @param \DateTime $from
     * @param \DateTime $to
     * @return int
     */
    public function getVolume(UserId $userId, \DateTime $from, \DateTime $to): int;

    /**
     * @param $userId
     * @param Currency $currency
     * @return int
     */
    public function getTotalAmountPerUserForCurrency(UserId $userId, Currency $currency): int;

    /**
     * @param $userId
     * @param \DateTime $from
     * @param \DateTime $to
     * @return int
     */
    public function countUserTransactionsInTimePeriod(UserId $userId, \DateTime $from, \DateTime $to): int;
}