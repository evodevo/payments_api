<?php

declare(strict_types=1);

namespace PaymentsAPI\Application\Command;

use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\Service\PaymentProviderFactory;

/**
 * Class ProcessTransactionHandler
 * @package PaymentsAPI\Application\Command
 */
class ProcessTransactionsHandler
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var PaymentProviderFactory
     */
    private $paymentProviderFactory;

    /**
     * ProcessTransactionHandler constructor.
     * @param TransactionRepository $transactionRepository
     * @param PaymentProviderFactory $paymentProviderFactory
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        PaymentProviderFactory $paymentProviderFactory
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->paymentProviderFactory = $paymentProviderFactory;
    }

    /**
     * @param ProcessTransactions $processTransactions
     * @return int
     */
    public function handle(ProcessTransactions $processTransactions)
    {
        $transactions = $this->transactionRepository->findConfirmed($processTransactions->getBatchSize());

        foreach ($transactions as $transaction) {
            $paymentProvider = $this->paymentProviderFactory->create($transaction);

            $paymentProvider->process($transaction);

            $this->transactionRepository->add($transaction);
        }

        return count($transactions);
    }
}