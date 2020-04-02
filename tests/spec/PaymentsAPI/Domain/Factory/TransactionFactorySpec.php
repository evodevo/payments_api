<?php

namespace spec\PaymentsAPI\Domain\Factory;

use Money\Currency;
use Money\Money;
use Money\MoneyParser;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Factory\TransactionFactory;
use PaymentsAPI\Domain\Service\CodeGenerator;
use PaymentsAPI\Domain\Service\FeeCalculator;
use PaymentsAPI\Domain\ValueObject\Recipient;
use PaymentsAPI\Domain\ValueObject\UserId;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * Class TransactionFactorySpec
 * @package spec\PaymentsAPI\Domain\Factory
 */
class TransactionFactorySpec extends ObjectBehavior
{
    /**
     * @param FeeCalculator|\PhpSpec\Wrapper\Collaborator $feeCalculator
     * @param CodeGenerator|\PhpSpec\Wrapper\Collaborator $codeGenerator
     * @param MoneyParser|\PhpSpec\Wrapper\Collaborator $moneyParser
     */
    function let(FeeCalculator $feeCalculator, CodeGenerator $codeGenerator, MoneyParser $moneyParser)
    {
        $this->beConstructedWith($feeCalculator, $codeGenerator, $moneyParser);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TransactionFactory::class);
    }

    /**
     * @param FeeCalculator|\PhpSpec\Wrapper\Collaborator $feeCalculator
     * @param CodeGenerator|\PhpSpec\Wrapper\Collaborator $codeGenerator
     * @throws \Exception
     */
    function it_creates_transaction(FeeCalculator $feeCalculator, CodeGenerator $codeGenerator)
    {
        $transaction = $this->getTestTransaction();

        $eur = new Currency('EUR');
        $fee = new Money('2', $eur);

        $this->givenItCalculatesTransactionFee(
            $feeCalculator,
            $transaction,
            $fee
        );
        $this->givenItGeneratesConfirmationCode($codeGenerator, 111);


        $money = new Money('200', $eur);
        $userId = new UserId(1);
        $recipient = new Recipient('12345', 'John Doe');
        $details = 'Transaction number one';
        $total = new Money('202', $eur);

        $result = $this->create($money, $userId, $recipient, $details);

        $result->getUserId()->shouldBe($userId);
        $result->getDetails()->shouldBe($details);
        $result->getRecipient()->shouldBe($recipient);
        $result->getAmount()->equals($money)->shouldBe(true);
        $result->getCurrency()->shouldBe($eur);
        $result->getFee()->equals($fee)->shouldBe(true);
        $result->getTotal()->equals($total)->shouldBe(true);
        $result->getStatus()->shouldBe(Transaction::STATUS_CREATED);
        $result->getCreatedAt()->shouldBeAnInstanceOf(\DateTime::class);
    }

    /**
     * @param FeeCalculator $feeCalculator
     * @param Transaction $transaction
     * @param $fee
     */
    private function givenItCalculatesTransactionFee(FeeCalculator $feeCalculator, Transaction $transaction, $fee)
    {
        $feeCalculator->calculate(Argument::any())->willReturn($fee)->shouldBeCalled();
    }

    /**
     * @param CodeGenerator $codeGenerator
     * @param $code
     */
    private function givenItGeneratesConfirmationCode(CodeGenerator $codeGenerator, $code)
    {
        $codeGenerator->generate()->willReturn($code);
    }

    /**
     * @return Transaction
     * @throws \Exception
     */
    private function getTestTransaction(): Transaction
    {
        return new Transaction(
            new UserId(1),
            new Money('200', new Currency('EUR')),
            new Recipient('12345', 'John Doe'),
            'Transaction number one',
            111
        );
    }
}
