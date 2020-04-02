<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Service\FeeCalculator;

use Money\Money;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Service\FeeCalculator;

/**
 * Class PercentageFeeCalculator
 * @package PaymentsAPI\Domain\Service\FeeCalculator
 */
class PercentageFeeCalculator implements FeeCalculator
{
    const DEFAULT_FEE_PERCENT = 10.0;

    /**
     * @var float
     */
    protected $feePercent;

    /**
     * FixedPercentageCalculator constructor.
     * @param float $feePercent
     */
    public function __construct(float $feePercent = self::DEFAULT_FEE_PERCENT)
    {
        $this->feePercent = $feePercent;
    }

    /**
     * @param Transaction $transaction
     * @return Money
     */
    public function calculate(Transaction $transaction): Money
    {
        return $transaction->getAmount()->multiply($this->feePercent)->divide(100);
    }
}