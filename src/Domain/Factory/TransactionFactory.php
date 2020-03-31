<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Factory;

use Money\Money;
use Money\MoneyParser;
use PaymentsAPI\Application\Command\CreateTransaction;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Service\CodeGenerator;
use PaymentsAPI\Domain\Service\FeeCalculator;
use PaymentsAPI\Domain\ValueObject\Recipient;
use PaymentsAPI\Domain\ValueObject\UserId;

/**
 * Class TransactionFactory
 * @package PaymentsAPI\Domain\Factory
 */
class TransactionFactory
{
    /**
     * @var FeeCalculator
     */
    private $feeCalculator;

    /**
     * @var CodeGenerator
     */
    private $codeGenerator;

    /**
     * @var MoneyParser
     */
    private $moneyParser;

    /**
     * TransactionFactory constructor.
     * @param FeeCalculator $feeCalculator
     * @param CodeGenerator $codeGenerator
     * @param MoneyParser $moneyParser
     */
    public function __construct(FeeCalculator $feeCalculator, CodeGenerator $codeGenerator, MoneyParser $moneyParser)
    {
        $this->feeCalculator = $feeCalculator;
        $this->codeGenerator = $codeGenerator;
        $this->moneyParser = $moneyParser;
    }

    /**
     * @param CreateTransaction $createTransaction
     * @return Transaction
     * @throws \Exception
     */
    public function createFromCommand(CreateTransaction $createTransaction): Transaction
    {
        $money = $this->moneyParser->parse(
            (string)$createTransaction->getAmount(),
            strtoupper($createTransaction->getCurrency())
        );

        return $this->create(
            $money,
            new UserId($createTransaction->getUserId()),
            new Recipient(
                $createTransaction->getRecipientAccount(),
                $createTransaction->getRecipientName()
            ),
            $createTransaction->getDetails()
        );
    }

    /**
     * @param Money $money
     * @param $userId
     * @param Recipient $recipient
     * @param string $details
     * @return Transaction
     * @throws \Exception
     */
    public function create(Money $money, UserId $userId, Recipient $recipient, string $details): Transaction
    {
        $transaction = new Transaction(
            $userId,
            $money,
            $recipient,
            $details,
            $this->codeGenerator->generate()
        );

        $transaction->updateFee($this->feeCalculator->calculate($transaction));

        return $transaction;
    }
}