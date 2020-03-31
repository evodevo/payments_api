<?php

namespace spec\PaymentsAPI\Application\Command;

use Money\Currency;
use Money\Money;
use PaymentsAPI\Application\Command\CreateTransaction;
use PaymentsAPI\Application\Command\CreateTransactionHandler;
use PaymentsAPI\Application\DataTransformer\TransactionTransformer;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Factory\TransactionFactory;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\Service\RateLimiter;
use PaymentsAPI\Domain\Service\TransactionValidator;
use PaymentsAPI\Domain\ValueObject\Recipient;
use PaymentsAPI\Domain\ValueObject\UserId;
use PhpSpec\ObjectBehavior;

/**
 * Class CreateTransactionHandlerSpec
 * @package spec\PaymentsAPI\Application\Command
 */
class CreateTransactionHandlerSpec extends ObjectBehavior
{
    /**
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     * @param TransactionValidator|\PhpSpec\Wrapper\Collaborator $transactionValidator
     * @param RateLimiter|\PhpSpec\Wrapper\Collaborator $rateLimiter
     * @param TransactionTransformer|\PhpSpec\Wrapper\Collaborator $transactionTransformer
     * @param TransactionFactory|\PhpSpec\Wrapper\Collaborator $transactionFactory
     */
    function let(
        TransactionRepository $transactionRepository,
        TransactionValidator $transactionValidator,
        RateLimiter $rateLimiter,
        TransactionTransformer $transactionTransformer,
        TransactionFactory $transactionFactory
    ) {
        $this->beConstructedWith(
            $transactionRepository,
            $transactionValidator,
            $rateLimiter,
            $transactionTransformer,
            $transactionFactory
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CreateTransactionHandler::class);
    }

    /**
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     * @param RateLimiter|\PhpSpec\Wrapper\Collaborator $rateLimiter
     * @param TransactionValidator|\PhpSpec\Wrapper\Collaborator $transactionValidator
     * @param TransactionTransformer|\PhpSpec\Wrapper\Collaborator $transactionTransformer
     * @param TransactionFactory|\PhpSpec\Wrapper\Collaborator $transactionFactory
     * @throws \Exception
     */
    function it_handles_create_transaction_commands(
        TransactionRepository $transactionRepository,
        RateLimiter $rateLimiter,
        TransactionValidator $transactionValidator,
        TransactionTransformer $transactionTransformer,
        TransactionFactory $transactionFactory
    ) {
        $transaction = $this->createTestTransaction();
        $transactionData = $this->createTransactionDataArray();
        $createTransactionCommand = $this->createTestCommand();

        $this->givenFactoryCreatesTransactionFromCommand($transactionFactory, $transaction, $createTransactionCommand);
        $this->givenTransformerTransformsTransaction($transactionTransformer, $transaction, $transactionData);

        $result = $this->handle($createTransactionCommand);
        $result->shouldBe($transactionData);

        $rateLimiter->limitRate($transaction->getUserId())->shouldHaveBeenCalled();
        $transactionValidator->validate($transaction)->shouldHaveBeenCalled();
        $transactionRepository->add($transaction)->shouldHaveBeenCalled();
    }

    /**
     * @param TransactionFactory $transactionFactory
     * @param CreateTransaction $createTransaction
     * @param Transaction $transaction
     * @throws \Exception
     */
    private function givenFactoryCreatesTransactionFromCommand(
        TransactionFactory $transactionFactory,
        Transaction $transaction,
        CreateTransaction $createTransaction
    ) {
        $transactionFactory->createFromCommand($createTransaction)
            ->willReturn($transaction);
    }

    /**
     * @param TransactionTransformer $transactionTransformer
     * @param Transaction $transaction
     * @param array $data
     */
    private function givenTransformerTransformsTransaction(
        TransactionTransformer $transactionTransformer,
        Transaction $transaction,
        array $data
    ) {
        $transactionTransformer->transform($transaction)->willReturn($data);
    }

    /**
     * @return Transaction
     * @throws \Exception
     */
    private function createTestTransaction()
    {
        return new Transaction(
            new UserId(1),
            new Money('200', new Currency('EUR')),
            new Recipient('12345', 'John Doe'),
            'Transaction number one',
            111
        );
    }

    /**
     * @return CreateTransaction
     */
    private function createTestCommand(): CreateTransaction
    {
        return new CreateTransaction(
            1,
            111,
            '12345',
            'John Doe',
            '200',
            'EUR'
        );
    }

    /**
     * @return array
     */
    private function createTransactionDataArray(): array
    {
        return [
            'transaction_id' => 1,
            'amount' => '2.00',
            'fee' => '0.20',
        ];
    }
}
