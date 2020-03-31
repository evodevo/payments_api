<?php

namespace spec\PaymentsAPI\Infrastructure\Service\Payment\Factory;

use Money\Currency;
use Money\Money;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\ValueObject\Recipient;
use PaymentsAPI\Domain\ValueObject\UserId;
use PaymentsAPI\Infrastructure\Service\Payment\Factory\CurrencyBasedFactory;
use PaymentsAPI\Infrastructure\Service\Payment\MegaCash;
use PaymentsAPI\Infrastructure\Service\Payment\SuperMoney;
use PhpSpec\ObjectBehavior;

/**
 * Class CurrencyBasedFactorySpec
 * @package spec\PaymentsAPI\Infrastructure\Service\Payment\Factory
 */
class CurrencyBasedFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CurrencyBasedFactory::class);
    }

    /**
     * @throws \Exception
     */
    function it_returns_megacash_instance_for_eur_transactions()
    {
        $transaction = $this->givenEurTransaction();

        $result = $this->create($transaction);

        $result->shouldBeAnInstanceOf(MegaCash::class);
    }

    /**
     * @throws \Exception
     */
    function it_returns_supermoney_instance_for_non_eur_transactions()
    {
        $transaction = $this->givenUsdTransaction();

        $result = $this->create($transaction);

        $result->shouldBeAnInstanceOf(SuperMoney::class);
    }

    /**
     * @return Transaction
     * @throws \Exception
     */
    private function givenEurTransaction(): Transaction
    {
        return new Transaction(
            new UserId(1),
            new Money('200', new Currency('EUR')),
            new Recipient('12345', 'John Doe'),
            'Transaction number one',
            111
        );
    }

    /**
     * @return Transaction
     * @throws \Exception
     */
    private function givenUsdTransaction(): Transaction
    {
        return new Transaction(
            new UserId(1),
            new Money('200', new Currency('USD')),
            new Recipient('12345', 'John Doe'),
            'Transaction number one',
            111
        );
    }
}
