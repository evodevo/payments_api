<?php

declare(strict_types=1);

namespace PaymentsAPI\Application\Command;

/**
 * Class ProcessTransactions
 * @package PaymentsAPI\Application\Command
 */
class ProcessTransactions
{
    /**
     * @var int|null
     */
    private $batchSize;

    /**
     * ProcessTransactions constructor.
     * @param int|null $batchSize
     */
    public function __construct(int $batchSize = null)
    {
        $this->batchSize = $batchSize;
    }

    /**
     * @return int|null
     */
    public function getBatchSize(): int
    {
        return $this->batchSize;
    }
}