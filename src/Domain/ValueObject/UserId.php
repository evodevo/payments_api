<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\ValueObject;

/**
 * Class UserId
 * @package PaymentsAPI\Domain\ValueObject
 */
class UserId
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
            throw new \InvalidArgumentException('User id must be a positive integer');
        }

        $this->id = $id;
    }

    /**
     * @param UserId $other
     * @return bool
     */
    public function equals(UserId $other): bool
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