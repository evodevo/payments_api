<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Service;

use PaymentsAPI\Domain\Entity\Transaction;

/**
 * Interface PaymentProviderFactory
 * @package PaymentsAPI\Domain\Service
 */
interface PaymentProviderFactory
{
    /**
     * @param Transaction $transaction
     * @return PaymentProvider
     */
    public function create(Transaction $transaction): PaymentProvider;
}