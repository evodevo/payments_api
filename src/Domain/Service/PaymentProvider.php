<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Service;

use PaymentsAPI\Domain\Entity\Transaction;

/**
 * Interface PaymentProvider
 * @package PaymentsAPI\Domain\Service
 */
interface PaymentProvider
{
    /**
     * @param Transaction $transaction
     * @return mixed
     */
    public function process(Transaction $transaction);
}