<?php

namespace spec\PaymentsAPI\Domain\Service\FeeCalculator;

use Money\Currency;
use Money\Money;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\Service\FeeCalculator;
use PaymentsAPI\Domain\Service\FeeCalculator\VolumeDiscountedCalculator;
use PaymentsAPI\Domain\ValueObject\Recipient;
use PaymentsAPI\Domain\ValueObject\UserId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class VolumeDiscountedCalculatorSpec
 * @package spec\PaymentsAPI\Domain\Service\FeeCalculator
 */
class VolumeDiscountedCalculatorSpec extends ObjectBehavior
{
    /**
     * @param FeeCalculator|\PhpSpec\Wrapper\Collaborator $feeCalculator
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     */
    function let(FeeCalculator $feeCalculator, TransactionRepository $transactionRepository)
    {
        $this->beConstructedWith($feeCalculator, $transactionRepository, 5.0, 100, '1 day');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(VolumeDiscountedCalculator::class);
    }

    /**
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     * @throws \Exception
     */
    function it_applies_volume_discount(TransactionRepository $transactionRepository)
    {
        $transaction = $this->givenTransaction();
        $this->givenHighUserTransactionVolume($transactionRepository, $transaction->getUserId());

        $discountedFee = new Money('10', new Currency('EUR'));

        $result = $this->calculate($transaction);

        $result->equals($discountedFee)->shouldBe(true);
    }

    /**
     * @param FeeCalculator|\PhpSpec\Wrapper\Collaborator $feeCalculator
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     * @throws \Exception
     */
    function it_uses_fallback_calculator_when_volume_is_too_low(
        FeeCalculator $feeCalculator,
        TransactionRepository $transactionRepository
    ) {
        $transaction = $this->givenTransaction();
        $this->givenLowUserTransactionVolume($transactionRepository, $transaction->getUserId());

        $fee = new Money('20', new Currency('EUR'));
        $feeCalculator->calculate($transaction)->willReturn($fee)->shouldBeCalled();

        $result = $this->calculate($transaction);

        $result->equals($fee)->shouldBe(true);
    }

    /**
     * @param TransactionRepository $transactionRepository
     * @param UserId $userId
     */
    private function givenHighUserTransactionVolume(TransactionRepository $transactionRepository, UserId $userId)
    {
        $transactionRepository->getVolume($userId, Argument::any(), Argument::any())->willReturn(10001);
    }

    /**
     * @param TransactionRepository $transactionRepository
     * @param UserId $userId
     */
    private function givenLowUserTransactionVolume(TransactionRepository $transactionRepository, UserId $userId)
    {
        $transactionRepository->getVolume($userId, Argument::any(), Argument::any())->willReturn(1000);
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
