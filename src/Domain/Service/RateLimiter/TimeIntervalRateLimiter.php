<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Service\RateLimiter;

use PaymentsAPI\Domain\Exception\TooManyTransactions;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\Service\RateLimiter;
use PaymentsAPI\Domain\ValueObject\UserId;

/**
 * Class TimeIntervalRateLimiter
 * @package PaymentsAPI\Domain\Service\RateLimiter
 */
class TimeIntervalRateLimiter implements RateLimiter
{
    const DEFAULT_MAX_TRANSACTIONS = 10;

    const DEFAULT_TIME_PERIOD = '1 hour';

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var int
     */
    private $maxTransactions;

    /**
     * @var string
     */
    private $timePeriod;

    /**
     * TimeIntervalRateLimiter constructor.
     * @param TransactionRepository $transactionRepository
     * @param int $maxTransactions
     * @param string $timePeriod
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        $maxTransactions = self::DEFAULT_MAX_TRANSACTIONS,
        $timePeriod = self::DEFAULT_TIME_PERIOD
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->maxTransactions = $maxTransactions;
        $this->timePeriod = $timePeriod;
    }

    /**
     * @param UserId $userId
     * @return mixed|void
     * @throws \Exception
     */
    public function limitRate(UserId $userId)
    {
        if ($this->exceedsRate($userId)) {
            throw new TooManyTransactions(
                'Exceeded allowed rate of ' . $this->maxTransactions . ' transactions per ' . $this->timePeriod
            );
        }
    }

    /**
     * @param $userId
     * @return bool
     * @throws \Exception
     */
    public function exceedsRate($userId): bool
    {
        $currentTime = new \DateTime();

        $startTime = (clone $currentTime)->sub(\DateInterval::createFromDateString($this->timePeriod));
        $endTime = $currentTime;

        $transactionsCount = $this->transactionRepository->countUserTransactionsInTimePeriod(
            $userId,
            $startTime,
            $endTime
        );
        if ($transactionsCount >= $this->maxTransactions) {
            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getMaxTransactions(): int
    {
        return $this->maxTransactions;
    }
}