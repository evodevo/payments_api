<?php

declare(strict_types=1);

namespace PaymentsAPI\Application\Query;

use Doctrine\ORM\EntityNotFoundException;
use PaymentsAPI\Application\DataTransformer\TransactionTransformer;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\ValueObject\TransactionId;

/**
 * Class GetTransactionHandler
 * @package PaymentsAPI\Application\Query
 */
class GetTransactionHandler
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var TransactionTransformer
     */
    private $transactionTransformer;

    /**
     * GetTransactionHandler constructor.
     * @param TransactionRepository $transactionRepository
     * @param TransactionTransformer $transactionTransformer
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        TransactionTransformer $transactionTransformer
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionTransformer = $transactionTransformer;
    }

    /**
     * @param GetTransaction $getTransaction
     * @return array
     * @throws EntityNotFoundException
     */
    public function handle(GetTransaction $getTransaction)
    {
        $transactionId = new TransactionId($getTransaction->getTransactionId());

        $transaction = $this->transactionRepository->find($transactionId);
        if (!$transaction) {
            throw new EntityNotFoundException('Transaction with id '.$transactionId.' does not exist');
        }

        return $this->transactionTransformer->transform($transaction);
    }
}