<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\ValueObject;

/**
 * Class Recipient
 * @package PaymentsAPI\Domain\ValueObject
 */
class Recipient
{
    /**
     * @var string
     */
    private $account;

    /**
     * @var string
     */
    private $name;

    /**
     * Recipient constructor.
     * @param string $account
     * @param string $name
     */
    public function __construct(string $account, string $name)
    {
        if (!$account) {
            throw new \InvalidArgumentException('Recipient account cannot be empty');
        }

        if (!$name) {
            throw new \InvalidArgumentException('Recipient name cannot be empty');
        }

        $this->account = $account;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getAccount(): string
    {
        return $this->account;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}