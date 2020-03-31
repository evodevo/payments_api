<?php

declare(strict_types=1);

namespace PaymentsAPI\Application\Command;

use PaymentsAPI\Application\DataTransformer\TransactionTransformer;
use PaymentsAPI\Domain\Factory\TransactionFactory;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\Service\RateLimiter;
use PaymentsAPI\Domain\Service\TransactionValidator;
use PaymentsAPI\Domain\ValueObject\UserId;

/**
 * Class CreateTransactionHandler
 * @package PaymentsAPI\Application\Command
 */
class CreateTransactionHandler
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var TransactionValidator
     */
    private $transactionValidator;

    /**
     * @var RateLimiter
     */
    private $rateLimiter;

    /**
     * @var TransactionTransformer
     */
    private $transactionTransformer;

    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    /**
     * CreateTransactionHandler constructor.
     * @param TransactionRepository $transactionRepository
     * @param TransactionValidator $transactionValidator
     * @param RateLimiter $rateLimiter
     * @param TransactionTransformer $transactionTransformer
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        TransactionValidator $transactionValidator,
        RateLimiter $rateLimiter,
        TransactionTransformer $transactionTransformer,
        TransactionFactory $transactionFactory
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->transactionValidator = $transactionValidator;
        $this->rateLimiter = $rateLimiter;
        $this->transactionTransformer = $transactionTransformer;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @param CreateTransaction $createTransaction
     * @return array
     * @throws \Exception
     */
    public function handle(CreateTransaction $createTransaction)
    {
        $this->rateLimiter->limitRate(new UserId($createTransaction->getUserId()));

        $transaction = $this->transactionFactory->createFromCommand($createTransaction);

        $this->transactionValidator->validate($transaction);

        $this->transactionRepository->add($transaction);

        return $this->transactionTransformer->transform($transaction);
    }
}