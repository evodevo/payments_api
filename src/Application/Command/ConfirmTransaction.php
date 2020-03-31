<?php

declare(strict_types=1);

namespace PaymentsAPI\Application\Command;

use JMS\Serializer\Annotation as Serializer;

/**
 * Class ConfirmTransaction
 * @package PaymentsAPI\Application\Command
 */
class ConfirmTransaction
{
    /**
     * @var int
     */
    private $transactionId;

    /**
     * @var int
     */
    private $code;

    /**
     * ConfirmTransaction constructor.
     * @param int $transactionId
     * @param int $code
     */
    public function __construct(int $transactionId, int $code)
    {
        $this->transactionId = $transactionId;
        $this->code = $code;
    }

    /**
     * @return int
     * @Serializer\Type("integer")
     */
    public function getTransactionId(): int
    {
        return $this->transactionId;
    }

    /**
     * @return int
     * @Serializer\Type("integer")
     */
    public function getCode(): int
    {
        return $this->code;
    }
}