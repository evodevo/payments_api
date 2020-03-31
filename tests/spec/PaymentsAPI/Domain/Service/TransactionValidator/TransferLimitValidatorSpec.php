<?php

namespace spec\PaymentsAPI\Domain\Service\TransactionValidator;

use Money\Currency;
use Money\Money;
use Money\MoneyFormatter;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Exception\TransferLimitReached;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\Service\TransactionValidator\TransferLimitValidator;
use PaymentsAPI\Domain\ValueObject\Recipient;
use PaymentsAPI\Domain\ValueObject\UserId;
use PhpSpec\ObjectBehavior;

/**
 * Class TransferLimitValidatorSpec
 * @package spec\PaymentsAPI\Domain\Service\TransactionValidator
 */
class TransferLimitValidatorSpec extends ObjectBehavior
{
    /**
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     * @param MoneyFormatter|\PhpSpec\Wrapper\Collaborator $moneyFormatter
     */
    function let(TransactionRepository $transactionRepository, MoneyFormatter $moneyFormatter)
    {
        $this->beConstructedWith($transactionRepository, $moneyFormatter, 1000);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TransferLimitValidator::class);
    }

    /**
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     * @throws \Exception
     */
    function it_should_throw_exception_when_currency_transfer_limit_is_exceeded(
        TransactionRepository $transactionRepository
    ) {
        $transaction = $this->givenTransaction();
        $this->givenUserTransferredAmountInCurrency(
            $transactionRepository,
            $transaction->getUserId(),
            $transaction->getCurrency(),
            100000
        );

        $this->shouldThrow(TransferLimitReached::class)->duringValidate($transaction);
    }

    /**
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     * @throws \Exception
     */
    function it_should_not_throw_exception_when_currency_transfer_limit_is_not_exceeded(
        TransactionRepository $transactionRepository
    ) {
        $transaction = $this->givenTransaction();
        $this->givenUserTransferredAmountInCurrency(
            $transactionRepository,
            $transaction->getUserId(),
            $transaction->getCurrency(),
            80000
        );

        $this->shouldNotThrow(TransferLimitReached::class)->duringValidate($transaction);
    }

    /**
     * @param TransactionRepository $transactionRepository
     * @param UserId $userId
     * @param Currency $currency
     * @param $amount
     */
    private function givenUserTransferredAmountInCurrency(
        TransactionRepository $transactionRepository,
        UserId $userId,
        Currency $currency,
        $amount
    ) {
        $transactionRepository->getTotalAmountPerUserForCurrency($userId, $currency)->willReturn($amount);
    }

    /**
     * @return Transaction
     * @throws \Exception
     */
    private function givenTransaction(): Transaction
    {
        return new Transaction(
            new UserId(1),
            new Money('200', new Currency('EUR')),
            new Recipient('12345', 'John Doe'),
            'Transaction number one',
            111
        );
    }
}
