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
     * @param FeeCalculator|\PhpSpec\Wrapper\Collaborator $defaultFeeCalculator
     * @param FeeCalculator|\PhpSpec\Wrapper\Collaborator $discountedFeeCalculator
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     */
    function let(
        FeeCalculator $defaultFeeCalculator,
        FeeCalculator $discountedFeeCalculator,
        TransactionRepository $transactionRepository
    ) {
        $this->beConstructedWith(
            $defaultFeeCalculator,
            $discountedFeeCalculator,
            $transactionRepository
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(VolumeDiscountedCalculator::class);
    }

    /**
     * @param FeeCalculator|\PhpSpec\Wrapper\Collaborator $discountedFeeCalculator
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     * @throws \Exception
     */
    function it_uses_discounted_calculator_when_volume_is_high(
        FeeCalculator $discountedFeeCalculator,
        TransactionRepository $transactionRepository
    ) {
        $transaction = $this->createTransaction();
        $this->givenHighUserTransactionVolume($transactionRepository, $transaction->getUserId());

        $discountedFee = new Money('10', new Currency('EUR'));
        $this->givenCalculatorReturnsFeeForTransaction($discountedFeeCalculator, $discountedFee, $transaction);

        $result = $this->calculate($transaction);

        $result->equals($discountedFee)->shouldBe(true);
    }

    /**
     * @param FeeCalculator|\PhpSpec\Wrapper\Collaborator $defaultFeeCalculator
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     * @throws \Exception
     */
    function it_uses_standard_calculator_when_volume_is_low(
        FeeCalculator $defaultFeeCalculator,
        TransactionRepository $transactionRepository
    ) {
        $transaction = $this->createTransaction();
        $this->givenLowUserTransactionVolume($transactionRepository, $transaction->getUserId());

        $fee = new Money('20', new Currency('EUR'));
        $this->givenCalculatorReturnsFeeForTransaction($defaultFeeCalculator, $fee, $transaction);

        $result = $this->calculate($transaction);

        $result->equals($fee)->shouldBe(true);
    }

    private function givenCalculatorReturnsFeeForTransaction(FeeCalculator $feeCalculator, $fee, Transaction $transaction)
    {
        $feeCalculator->calculate($transaction)->willReturn($fee)->shouldBeCalled();
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
    private function createTransaction(): Transaction
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
