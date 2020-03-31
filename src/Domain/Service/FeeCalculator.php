<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Service;

use Money\Money;
use PaymentsAPI\Domain\Entity\Transaction;

/**
 * Interface FeeCalculator
 * @package PaymentsAPI\Domain\Service
 */
interface FeeCalculator
{
    /**
     * @param Transaction $transaction
     * @return mixed
     */
    public function calculate(Transaction $transaction): Money;
}