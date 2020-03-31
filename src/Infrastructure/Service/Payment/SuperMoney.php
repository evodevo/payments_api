<?php

declare(strict_types=1);

namespace PaymentsAPI\Infrastructure\Service\Payment;

use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Service\PaymentProvider;

/**
 * Class SuperMoney
 * @package PaymentsAPI\Infrastructure\Service\Payment
 */
class SuperMoney implements PaymentProvider
{
    const RANDOM_SEED = 42;

    /**
     * SuperMoney constructor.
     * @param int $seed
     */
    public function __construct($seed = self::RANDOM_SEED)
    {
        srand($seed);
    }

    /**
     * @param Transaction $transaction
     * @return mixed|void
     */
    public function process(Transaction $transaction)
    {
        $transaction->updateDetails(
            $transaction->getDetails() . ' ' . rand()
        );

        $transaction->complete();
    }
}