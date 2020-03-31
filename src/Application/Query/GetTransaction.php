<?php

declare(strict_types=1);

namespace PaymentsAPI\Application\Query;

/**
 * Class GetTransaction
 * @package PaymentsAPI\Application\Query
 */
class GetTransaction
{
    /**
     * @var int
     */
    private $transactionId;

    /**
     * GetTransaction constructor.
     * @param int $transactionId
     */
    public function __construct(int $transactionId)
    {
        $this->transactionId = $transactionId;
    }

    /**
     * @return int
     */
    public function getTransactionId(): int
    {
        return $this->transactionId;
    }
}