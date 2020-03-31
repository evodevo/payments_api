<?php

declare(strict_types=1);

namespace PaymentsAPI\Application\Command;

use Doctrine\ORM\EntityNotFoundException;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\ValueObject\TransactionId;

/**
 * Class ConfirmTransactionHandler
 * @package PaymentsAPI\Application\Command
 */
class ConfirmTransactionHandler
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * ConfirmTransactionHandler constructor.
     * @param TransactionRepository $transactionRepository
     */
    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @param ConfirmTransaction $confirmTransaction
     * @throws EntityNotFoundException
     */
    public function handle(ConfirmTransaction $confirmTransaction)
    {
        $transactionId = new TransactionId($confirmTransaction->getTransactionId());

        $transaction = $this->transactionRepository->find($transactionId);
        if (!$transaction) {
            throw new EntityNotFoundException('Transaction with id ' . $transactionId . ' does not exist');
        }

        $transaction->confirm($confirmTransaction->getCode());

        $this->transactionRepository->add($transaction);
    }
}