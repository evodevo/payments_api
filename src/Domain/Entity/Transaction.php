<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Entity;

use Money\Currency;
use Money\Money;
use PaymentsAPI\Domain\Exception\ConfirmationFailed;
use PaymentsAPI\Domain\Exception\InvalidConfirmationCode;
use PaymentsAPI\Domain\ValueObject\Recipient;
use PaymentsAPI\Domain\ValueObject\TransactionId;
use PaymentsAPI\Domain\ValueObject\UserId;

/**
 * Class Transaction
 * @package PaymentsAPI\Domain\Entity
 */
class Transaction
{
    const STATUS_CREATED = 'created';
    const STATUS_CONFIRMED = 'confirmed';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';

    const DEFAULT_FEE_PERCENT = 10.0;

    /**
     * @var TransactionId
     */
    private $id;

    /**
     * @var UserId
     */
    private $userId;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var Currency
     */
    private $currency;

    /**
     * @var Recipient
     */
    private $recipient;

    /**
     * @var string
     */
    private $details;

    /**
     * @var int
     */
    private $confirmationCode;

    /**
     * @var string
     */
    private $status = self::STATUS_CREATED;

    /**
     * @var int
     */
    private $fee;

    /**
     * @var int
     */
    private $total;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * Transaction constructor.
     * @param UserId $userId
     * @param Money $money
     * @param Recipient $recipient
     * @param string $details
     * @param int $confirmationCode
     * @throws \Exception
     */
    public function __construct(
        UserId $userId,
        Money $money,
        Recipient $recipient,
        string $details,
        int $confirmationCode
    ) {
        if (!$money->greaterThan(new Money(0, $money->getCurrency()))) {
            throw new \InvalidArgumentException('Transfer amount cannot be negative');
        }

        $this->userId = $userId;
        $this->amount = $money->getAmount();
        $this->currency = $money->getCurrency();

        // Initialize transaction with zero fee.
        $this->applyFee(new Money(0, $this->currency));

        $this->recipient = $recipient;
        $this->details = $details;
        $this->confirmationCode = $confirmationCode;

        $this->createdAt = new \DateTime();
    }

    /**
     * @param Money $fee
     */
    public function applyFee(Money $fee)
    {
        if (!$fee->getCurrency()->equals($this->currency)) {
            throw new \InvalidArgumentException('Transaction fee must have the same currency as transaction');
        }

        if ($fee->lessThan(new Money(0, $this->currency))) {
            throw new \InvalidArgumentException('Fee cannot be negative');
        }

        if ($this->status !== self::STATUS_CREATED) {
            throw new \RuntimeException('Cannot update fee for an already confirmed transaction');
        }

        $this->fee = $fee->getAmount();
        $this->total = $this->calculateTotal()->getAmount();
    }

    /**
     * @return Money
     */
    private function calculateTotal()
    {
        return $this->getAmount()->add($this->getFee());
    }

    /**
     * @param int $code
     */
    public function confirm(int $code)
    {
        if ($this->status !== self::STATUS_CREATED) {
            throw new ConfirmationFailed('Only unconfirmed transactions can be confirmed');
        }

        if ($this->confirmationCode !== $code) {
            throw new InvalidConfirmationCode('Confirmation code is invalid');
        }

        $this->status = self::STATUS_CONFIRMED;
    }

    /**
     * Changes transaction status to completed.
     */
    public function complete()
    {
        if ($this->status === self::STATUS_CREATED) {
            throw new \RuntimeException('Cannot complete unconfirmed transaction');
        }

        $this->status = self::STATUS_COMPLETED;
    }

    /**
     * @param Money $amount
     * @return bool
     */
    public function exceedsAmount(Money $amount): bool
    {
        return $this->getTotal()->greaterThan($amount);
    }

    /**
     * @param $newDetails
     */
    public function updateDetails($newDetails)
    {
        $this->details = $newDetails;
    }

    /**
     * @param Currency $currency
     * @return bool
     */
    public function hasCurrency(Currency $currency): bool
    {
        return $this->currency->equals($currency);
    }

    /**
     * @return Money
     */
    public function getTotal(): Money
    {
        return new Money($this->total, $this->currency);
    }

    /**
     * @return Money
     */
    public function getAmount(): Money
    {
        return new Money($this->amount, $this->currency);
    }

    /**
     * @return Money
     */
    public function getFee(): Money
    {
        return new Money($this->fee, $this->currency);
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @return UserId
     */
    public function getUserId(): UserId
    {
        return $this->userId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @return TransactionId
     */
    public function getId(): TransactionId
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getDetails(): string
    {
        return $this->details;
    }

    /**
     * @return Recipient
     */
    public function getRecipient(): Recipient
    {
        return $this->recipient;
    }
}