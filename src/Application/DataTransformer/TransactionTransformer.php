<?php

declare(strict_types=1);

namespace PaymentsAPI\Application\DataTransformer;

use League\Fractal\TransformerAbstract;
use Money\Formatter\DecimalMoneyFormatter;
use Money\MoneyFormatter;
use PaymentsAPI\Domain\Entity\Transaction;

/**
 * Class TransactionTransformer
 * @package PaymentsAPI\Application\DataTransformer
 */
class TransactionTransformer extends TransformerAbstract
{
    /**
     * @var DecimalMoneyFormatter
     */
    private $moneyFormatter;

    /**
     * TransactionTransformer constructor.
     * @param MoneyFormatter $moneyFormatter
     */
    public function __construct(MoneyFormatter $moneyFormatter)
    {
        $this->moneyFormatter = $moneyFormatter;
    }

    /**
     * @param Transaction $transaction
     * @return array
     */
    public function transform(Transaction $transaction): array
    {
        return [
            'transaction_id' => intval((string)$transaction->getId()),
            'details' => $transaction->getDetails(),
            'recipient_account' => $transaction->getRecipient()->getAccount(),
            'recipient_name' => $transaction->getRecipient()->getName(),
            'amount' => $this->moneyFormatter->format($transaction->getAmount()),
            'currency' => strtolower((string)$transaction->getCurrency()),
            'fee' => $this->moneyFormatter->format($transaction->getFee()),
            'status' => $transaction->getStatus(),
            'created_at' => $transaction->getCreatedAt()->format(\DateTime::ATOM),
        ];
    }
}