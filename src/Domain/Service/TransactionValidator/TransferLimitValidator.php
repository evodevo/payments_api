<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Service\TransactionValidator;

use Money\Money;
use Money\MoneyFormatter;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Exception\TransferLimitReached;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\Service\TransactionValidator;

class TransferLimitValidator implements TransactionValidator
{
    /**
     * Allowed max transfer limit in cents.
     */
    const TRANSFER_LIMIT = 1000;

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var int
     */
    private $transferLimit;

    /**
     * @var MoneyFormatter
     */
    private $moneyFormatter;

    /**
     * TransferLimitValidator constructor.
     * @param TransactionRepository $transactionRepository
     * @param MoneyFormatter $moneyFormatter
     * @param int $transferLimit
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        MoneyFormatter $moneyFormatter,
        $transferLimit = self::TRANSFER_LIMIT
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->moneyFormatter = $moneyFormatter;
        $this->transferLimit = $transferLimit;
    }

    /**
     * @param Transaction $transaction
     * @return mixed|void
     */
    public function validate(Transaction $transaction)
    {
        $this->checkExceedsLimit($transaction);
    }

    /**
     * @return int
     */
    public function getTransferLimit(): int
    {
        return $this->transferLimit;
    }

    /**
     * @param Transaction $transaction
     */
    private function checkExceedsLimit(Transaction $transaction)
    {
        if (!$this->transferLimit) {
            return;
        }

        $remainingAmount = $this->getRemainingAmount($transaction);

        if ($transaction->exceedsAmount($remainingAmount)) {
            throw new TransferLimitReached(
                'Transaction exceeds the allowed remaining transfer amount of ' .
                $this->moneyFormatter->format($remainingAmount) . ' for currency ' . $transaction->getCurrency()
            );
        }
    }

    /**
     * @param Transaction $transaction
     * @return Money
     */
    private function getRemainingAmount(Transaction $transaction): Money
    {
        $transferredAmount = $this->transactionRepository->getTotalAmountPerUserForCurrency(
            $transaction->getUserId(),
            $transaction->getCurrency()
        );
        $transferredAmount = new Money($transferredAmount, $transaction->getCurrency());

        $transferLimit = new Money($this->transferLimit * 100, $transaction->getCurrency());

        return $transferLimit->subtract($transferredAmount);
    }
}