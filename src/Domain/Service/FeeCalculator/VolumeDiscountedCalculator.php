<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Service\FeeCalculator;

use Money\Money;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\Service\FeeCalculator;
use PaymentsAPI\Domain\ValueObject\UserId;

/**
 * Class VolumeDiscountedCalculator
 * @package PaymentsAPI\Domain\Service\FeeCalculator
 */
class VolumeDiscountedCalculator implements FeeCalculator
{
    const DISCOUNTED_FEE_PERCENT = 5.0;

    const DISCOUNT_ELIGIBILITY_THRESHOLD = 100;

    /**
     * @var FeeCalculator
     */
    protected $defaultFeeCalculator;

    /**
     * @var FeeCalculator
     */
    protected $discountedFeeCalculator;

    /**
     * @var TransactionRepository
     */
    protected $transactionRepository;

    /**
     * @var int
     */
    protected $discountEligibilityThreshold;

    /**
     * VolumeDiscountedCalculator constructor.
     * @param FeeCalculator $defaultFeeCalculator
     * @param FeeCalculator $discountedFeeCalculator
     * @param TransactionRepository $transactionRepository
//     * @param float $discountedFeePercent
     * @param int $discountEligibilityThreshold
     */
    public function __construct(
        FeeCalculator $defaultFeeCalculator,
        FeeCalculator $discountedFeeCalculator,
        TransactionRepository $transactionRepository,
        int $discountEligibilityThreshold = self::DISCOUNT_ELIGIBILITY_THRESHOLD
    ) {
        $this->defaultFeeCalculator = $defaultFeeCalculator;
        $this->discountedFeeCalculator = $discountedFeeCalculator;
        $this->transactionRepository = $transactionRepository;
        $this->discountEligibilityThreshold = $discountEligibilityThreshold;
    }

    /**
     * @param Transaction $transaction
     * @return Money
     * @throws \Exception
     */
    public function calculate(Transaction $transaction): Money
    {
        if ($this->isEligibleForVolumeDiscount($transaction->getUserId())) {
            return $this->discountedFeeCalculator->calculate($transaction);
        }

        return $this->defaultFeeCalculator->calculate($transaction);
    }

    /**
     * @param UserId $userId
     * @return bool
     * @throws \Exception
     */
    private function isEligibleForVolumeDiscount(UserId $userId): bool
    {
        $dailyVolume = $this->getDailyVolume($userId);
        if ($dailyVolume > $this->discountEligibilityThreshold * 100) {
            return true;
        }

        return false;
    }

    /**
     * @param UserId $userId
     * @return int
     * @throws \Exception
     */
    private function getDailyVolume(UserId $userId): int
    {
        $startDate = new \DateTime('today');
        $endDate = (clone $startDate)->setTime(23, 59, 59, 999);

        return $this->transactionRepository->getVolume($userId, $startDate, $endDate);
    }
}