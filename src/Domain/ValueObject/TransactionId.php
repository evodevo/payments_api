<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\ValueObject;

/**
 * Class TransactionId
 * @package PaymentsAPI\Domain\ValueObject
 */
class TransactionId
{
    /**
     * @var int
     */
    private $id;

    /**
     * UserId constructor.
     * @param int $id
     */
    public function __construct(int $id)
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Transaction id must be a positive integer');
        }

        $this->id = $id;
    }

    /**
     * @param TransactionId $other
     * @return bool
     */
    public function equals(TransactionId $other): bool
    {
        return $this->id === $other->getId();
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string)$this->id;
    }
}