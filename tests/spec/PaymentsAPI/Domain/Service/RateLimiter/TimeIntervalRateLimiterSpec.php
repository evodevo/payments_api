<?php

namespace spec\PaymentsAPI\Domain\Service\RateLimiter;

use PaymentsAPI\Domain\Exception\TooManyTransactions;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\Service\RateLimiter\TimeIntervalRateLimiter;
use PaymentsAPI\Domain\ValueObject\UserId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TimeIntervalRateLimiterSpec
 * @package spec\PaymentsAPI\Domain\Service\RateLimiter
 */
class TimeIntervalRateLimiterSpec extends ObjectBehavior
{
    /**
     * @param TransactionRepository $transactionRepository
     */
    function let(TransactionRepository $transactionRepository)
    {
        $this->beConstructedWith($transactionRepository, 10, '1 hour');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TimeIntervalRateLimiter::class);
    }

    /**
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     * @throws \Exception
     */
    function it_throws_exception_when_rate_is_exceeded(TransactionRepository $transactionRepository)
    {
        $userId = new UserId(1);
        $this->givenUserCreatedTransactions($transactionRepository,$userId, 10);

        $this->shouldThrow(TooManyTransactions::class)->duringLimitRate($userId);
    }

    /**
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     * @throws \Exception
     */
    function it_does_not_throw_exception_when_rate_is_not_exceeded(TransactionRepository $transactionRepository)
    {
        $userId = new UserId(1);
        $this->givenUserCreatedTransactions($transactionRepository, $userId, 9);

        $this->shouldNotThrow(TooManyTransactions::class)->duringLimitRate($userId);
    }

    /**
     * @param TransactionRepository $transactionRepository
     * @param UserId $userId
     * @param $numTransactions
     */
    private function givenUserCreatedTransactions(TransactionRepository $transactionRepository, UserId $userId, $numTransactions)
    {
        $transactionRepository->countUserTransactionsInTimePeriod(
            $userId,
            Argument::any(),
            Argument::any()
        )->willReturn($numTransactions);
    }
}
