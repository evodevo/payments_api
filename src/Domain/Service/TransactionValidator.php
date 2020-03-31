<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Service;

use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Exception\TransferLimitReached;

/**
 * Interface RateLimiter
 * @package PaymentsAPI\Domain\Service
 */
interface TransactionValidator
{
    /**
     * @param Transaction $transaction
     * @throws TransferLimitReached
     * @return void
     */
    public function validate(Transaction $transaction);
}