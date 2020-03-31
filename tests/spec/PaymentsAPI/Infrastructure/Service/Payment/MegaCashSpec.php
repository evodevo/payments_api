<?php

namespace spec\PaymentsAPI\Infrastructure\Service\Payment;

use Money\Currency;
use Money\Money;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\ValueObject\Recipient;
use PaymentsAPI\Domain\ValueObject\UserId;
use PaymentsAPI\Infrastructure\Service\Payment\MegaCash;
use PhpSpec\ObjectBehavior;

/**
 * Class MegaCashSpec
 * @package spec\PaymentsAPI\Infrastructure\Service\Payment
 */
class MegaCashSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(MegaCash::class);
    }

    /**
     * @throws \Exception
     */
    function it_processes_transactions()
    {
        $transaction = $this->givenTransaction();

        $this->process($transaction);

        expect($transaction->getDetails())->shouldBe('Transaction number o');
        expect($transaction->getStatus())->shouldBe(Transaction::STATUS_COMPLETED);
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
