<?php

declare(strict_types=1);

namespace PaymentsAPI\Application\Command;

use JMS\Serializer\Annotation as Serializer;
use Symfony\Component\Validator\Constraints as Assert;

class CreateTransaction
{
    /**
     * @var int
     * @Serializer\Type("integer")
     * @Assert\Type("integer")
     * @Assert\NotBlank
     * @Assert\Positive(message="must be positive integer")
     */
    private $userId;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Assert\NotBlank
     * @Assert\Length(max="255")
     */
    private $details;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Assert\NotBlank
     * @Assert\Length(max=64)
     */
    private $recipientAccount;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Assert\NotBlank
     * @Assert\Length(max="64")
     */
    private $recipientName;

    /**
     * @var float
     * @Serializer\Type("float")
     * @Assert\Type("float")
     * @Assert\NotBlank
     * @Assert\GreaterThan(0, message="must be greater than {{ compared_value }}")
     */
    private $amount;

    /**
     * @var string
     * @Serializer\Type("string")
     * @Assert\NotBlank
     * @Assert\Length(3)
     */
    private $currency;

    /**
     * CreateTransaction constructor.
     * @param int $userId
     * @param string $details
     * @param string $recipientAccount
     * @param string $recipientName
     * @param float $amount
     * @param string $currency
     */
    public function __construct(int $userId, string $details, string $recipientAccount, string $recipientName, float $amount, string $currency)
    {
        $this->userId = $userId;
        $this->details = $details;
        $this->recipientAccount = $recipientAccount;
        $this->recipientName = $recipientName;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getDetails(): string
    {
        return $this->details;
    }

    /**
     * @return string
     */
    public function getRecipientAccount(): string
    {
        return $this->recipientAccount;
    }

    /**
     * @return string
     */
    public function getRecipientName(): string
    {
        return $this->recipientName;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }
}