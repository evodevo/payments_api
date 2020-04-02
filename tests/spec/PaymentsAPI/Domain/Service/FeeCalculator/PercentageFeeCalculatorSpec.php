<?php

namespace spec\PaymentsAPI\Domain\Service\FeeCalculator;

use Money\Currency;
use Money\Money;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Service\FeeCalculator\PercentageFeeCalculator;
use PaymentsAPI\Domain\ValueObject\Recipient;
use PaymentsAPI\Domain\ValueObject\UserId;
use PhpSpec\ObjectBehavior;

/**
 * Class FixedPercentageCalculatorSpec
 * @package spec\PaymentsAPI\Domain\Service\FeeCalculator
 */
class PercentageFeeCalculatorSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(10.0);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PercentageFeeCalculator::class);
    }

    /**
     * @throws \Exception
     */
    function it_calculates_transaction_fee()
    {
        $transaction = $this->givenTransaction();
        $fee = new Money('20', new Currency('EUR'));

        $result = $this->calculate($transaction);

        $result->equals($fee)->shouldBe(true);
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
