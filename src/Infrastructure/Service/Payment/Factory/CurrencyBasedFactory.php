<?php

declare(strict_types=1);

namespace PaymentsAPI\Infrastructure\Service\Payment\Factory;

use Money\Currency;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Service\PaymentProvider;
use PaymentsAPI\Domain\Service\PaymentProviderFactory;
use PaymentsAPI\Infrastructure\Service\Payment\MegaCash;
use PaymentsAPI\Infrastructure\Service\Payment\SuperMoney;

/**
 * Class CurrencyBasedFactory
 * @package PaymentsAPI\Infrastructure\Service\Payment\Factory
 */
class CurrencyBasedFactory implements PaymentProviderFactory
{
    /**
     * @param Transaction $transaction
     * @return MegaCash|SuperMoney
     */
    public function create(Transaction $transaction): PaymentProvider
    {
        if ($transaction->hasCurrency(new Currency('EUR'))) {
            return new MegaCash();
        }

        return new SuperMoney();
    }
}