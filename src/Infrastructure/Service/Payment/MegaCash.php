<?php

declare(strict_types=1);

namespace PaymentsAPI\Infrastructure\Service\Payment;

use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Service\PaymentProvider;

/**
 * Class MegaCash
 * @package PaymentsAPI\Infrastructure\Service\Payment
 */
class MegaCash implements PaymentProvider
{
    /**
     * @param Transaction $transaction
     * @return mixed|void
     */
    public function process(Transaction $transaction)
    {
        $transaction->updateDetails(
            substr($transaction->getDetails(), 0, 20)
        );

        $transaction->complete();
    }
}