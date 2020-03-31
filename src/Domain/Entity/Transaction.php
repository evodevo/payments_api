<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Entity;

use Money\Currency;
use Money\Money;
use PaymentsAPI\Domain\Exception\AlreadyConfirmed;
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

    /**
     * @var TransactionId
     */
    private $id;

    /**
     * @var UserId
     */
    private $userId;

    /**
     * @var Money
     */
    private $money;

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
     * @var Money
     */
    private $fee;

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
        $this->userId = $userId;
        $this->money = $money;
        $this->recipient = $recipient;
        $this->details = $details;
        $this->confirmationCode = $confirmationCode;
        $this->fee = new Money(0, $money->getCurrency());
        $this->createdAt = new \DateTime();
    }

    /**
     * @param int $code
     */
    public function confirm(int $code)
    {
        if ($this->confirmationCode !== $code) {
            throw new InvalidConfirmationCode('Confirmation code is invalid');
        }

        if (!$this->isWaitingConfirmation()) {
            throw new AlreadyConfirmed('Transaction with code '.$code.' is already confirmed');
        }

        $this->status = self::STATUS_CONFIRMED;
    }

    /**
     * Changes transaction status to processed.
     */
    public function complete()
    {
        $this->status = self::STATUS_COMPLETED;
    }

    /**
     * @param Money $amount
     * @return bool
     */
    public function exceedsAmount(Money $amount): bool
    {
        return $this->money->greaterThan($amount);
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

    /**
     * @param $newDetails
     */
    public function updateDetails($newDetails)
    {
        $this->details = $newDetails;
    }

    /**
     * @param Money $fee
     */
    public function updateFee(Money $fee)
    {
        $this->fee = $fee;
    }

    /**
     * @param Currency $currency
     * @return bool
     */
    public function hasCurrency(Currency $currency): bool
    {
        return $this->money->getCurrency()->equals($currency);
    }

    /**
     * @return Money
     */
    public function getMoney(): Money
    {
        return $this->money;
    }

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->money->getCurrency();
    }

    /**
     * @return Money
     */
    public function getFee(): Money
    {
        return $this->fee;
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
     * @return bool
     */
    private function isWaitingConfirmation(): bool
    {
        return $this->status === self::STATUS_CREATED;
    }
}