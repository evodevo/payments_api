<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Service;

use PaymentsAPI\Domain\Exception\TooManyTransactions;
use PaymentsAPI\Domain\ValueObject\UserId;

/**
 * Interface RateLimiter
 * @package PaymentsAPI\Domain\Service
 */
interface RateLimiter
{
    /**
     * @param $userId
     * @throws TooManyTransactions
     * @return void
     */
    public function limitRate(UserId $userId);
}