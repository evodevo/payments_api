<?php

declare(strict_types=1);

namespace PaymentsAPI\Infrastructure\Console\Command;

use PaymentsAPI\Application\Command\ProcessTransactions;
use PaymentsAPI\Application\Command\ProcessTransactionsHandler;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ProcessPayments
 * @package PaymentsAPI\Infrastructure\Console\Command
 */
class ProcessPayments extends Command
{
    const BATCH_SIZE = 100;

    /**
     * @var string
     */
    protected static $defaultName = 'payments:process';

    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var ProcessTransactionsHandler
     */
    private $processTransactionHandler;

    /**
     * ProcessPayments constructor.
     * @param TransactionRepository $transactionRepository
     * @param ProcessTransactionsHandler $processTransactionHandler
     */
    public function __construct(
        TransactionRepository $transactionRepository,
        ProcessTransactionsHandler $processTransactionHandler
    ) {
        $this->transactionRepository = $transactionRepository;
        $this->processTransactionHandler = $processTransactionHandler;

        parent::__construct();
    }

    protected function configure()
    {
        $this
            ->addArgument(
                'batch_size',
                InputArgument::OPTIONAL,
                'Transactions batch size',
                self::BATCH_SIZE
            )
            ->setDescription('Process confirmed transactions.')
            ->setHelp('This command processes confirmed transactions in batches.')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('Started processing transactions');

        $batchSize = (int)$input->getArgument('batch_size');

        $totalConfirmed = $this->transactionRepository->getTotalConfirmed();

        $totalProcessed = $processedBatchSize = $this->processTransactionHandler->handle($batchSize);

        $output->writeln("Processed [{$totalProcessed}/{$totalConfirmed}]");

        while ($processedBatchSize && $totalProcessed < $totalConfirmed) {
            $processedBatchSize = $this->processTransactionHandler->handle(
                new ProcessTransactions($batchSize)
            );

            $totalProcessed += $processedBatchSize;

            $output->writeln("Processed [{$totalProcessed}/{$totalConfirmed}]");
        }

        $output->writeln('Finished processing transactions');

        return 0;
    }
}