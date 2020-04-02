<?php

namespace spec\PaymentsAPI\Infrastructure\Service\Payment;

use Money\Currency;
use Money\Money;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\ValueObject\Recipient;
use PaymentsAPI\Domain\ValueObject\UserId;
use PaymentsAPI\Infrastructure\Service\Payment\SuperMoney;
use PhpSpec\ObjectBehavior;

/**
 * Class SuperMoneySpec
 * @package spec\PaymentsAPI\Infrastructure\Service\Payment
 */
class SuperMoneySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(SuperMoney::class);
    }

    /**
     * @throws \Exception
     */
    function it_processes_transactions()
    {
        $transaction = $this->createConfirmedTransaction();

        $this->process($transaction);

        expect($transaction->getDetails())->shouldBe('Transaction number one 804318771');
        expect($transaction->getStatus())->shouldBeEqualTo(Transaction::STATUS_COMPLETED);
    }

    /**
     * @return Transaction
     * @throws \Exception
     */
    private function createConfirmedTransaction(): Transaction
    {
        $transaction = new Transaction(
            new UserId(1),
            new Money('200', new Currency('EUR')),
            new Recipient('12345', 'John Doe'),
            'Transaction number one',
            111
        );
        $transaction->confirm(111);

        return $transaction;
    }
}
