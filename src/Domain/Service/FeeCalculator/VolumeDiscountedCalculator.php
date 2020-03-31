<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Service\FeeCalculator;

use Money\Money;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\Service\FeeCalculator;

/**
 * Class VolumeDiscountedCalculator
 * @package PaymentsAPI\Domain\Service\FeeCalculator
 */
class VolumeDiscountedCalculator implements FeeCalculator
{
    const DISCOUNTED_FEE_PERCENT = 5.0;

    const DISCOUNT_ELIGIBILITY_THRESHOLD = 100;

    const DISCOUNT_TIME_PERIOD = '1 day';

    /**
     * @var FeeCalculator
     */
    protected $feeCalculator;

    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var float
     */
    protected $discountedFeePercent;

    /**
     * @var int
     */
    protected $discountEligibilityThreshold;

    /**
     * @var string
     */
    protected $discountTimePeriod;

    /**
     * VolumeDiscountedCalculator constructor.
     * @param FeeCalculator $feeCalculator
     * @param TransactionRepository $transactionRepository
     * @param float $discountedFeePercent
     * @param int $discountEligibilityThreshold
     * @param string $discountTimePeriod
     */
    public function __construct(
        FeeCalculator $feeCalculator,
        TransactionRepository $transactionRepository,
        float $discountedFeePercent = self::DISCOUNTED_FEE_PERCENT,
        int $discountEligibilityThreshold = self::DISCOUNT_ELIGIBILITY_THRESHOLD,
        string $discountTimePeriod = self::DISCOUNT_TIME_PERIOD
    ) {
        $this->feeCalculator = $feeCalculator;
        $this->transactionRepository = $transactionRepository;
        $this->discountedFeePercent = $discountedFeePercent;
        $this->discountEligibilityThreshold = $discountEligibilityThreshold;
        $this->discountTimePeriod = $discountTimePeriod;
    }

    /**
     * @param Transaction $transaction
     * @return Money
     */
    public function calculate(Transaction $transaction): Money
    {
        if ($this->isEligibleForVolumeDiscount($transaction)) {
            return $transaction->getMoney()->multiply($this->discountedFeePercent / 100);
        }

        return $this->feeCalculator->calculate($transaction);
    }

    /**
     * @param Transaction $transaction
     * @return bool
     */
    private function isEligibleForVolumeDiscount(Transaction $transaction): bool
    {
        $dailyVolume = $this->getDailyVolume($transaction);
        if ($dailyVolume > $this->discountEligibilityThreshold * 100) {
            return true;
        }

        return false;
    }

    /**
     * @param Transaction $transaction
     * @return int
     */
    private function getDailyVolume(Transaction $transaction): int
    {
        $startTime = $transaction->getCreatedAt();
        $endTime = (clone $startTime)->add(\DateInterval::createFromDateString($this->discountTimePeriod));

        return $this->transactionRepository->getVolume($transaction->getUserId(), $startTime, $endTime);
    }
}