<?php

namespace spec\PaymentsAPI\Application\Command;

use Money\Currency;
use Money\Money;
use PaymentsAPI\Application\Command\ProcessTransactionsHandler;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\Service\PaymentProvider;
use PaymentsAPI\Domain\Service\PaymentProviderFactory;
use PaymentsAPI\Domain\ValueObject\Recipient;
use PaymentsAPI\Domain\ValueObject\UserId;
use PhpSpec\ObjectBehavior;

/**
 * Class ProcessTransactionsHandlerSpec
 * @package spec\PaymentsAPI\Application\Command
 */
class ProcessTransactionsHandlerSpec extends ObjectBehavior
{
    /**
     * @param TransactionRepository $transactionRepository
     * @param PaymentProviderFactory $paymentProviderFactory
     */
    function let(TransactionRepository $transactionRepository, PaymentProviderFactory $paymentProviderFactory)
    {
        $this->beConstructedWith($transactionRepository, $paymentProviderFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProcessTransactionsHandler::class);
    }

    /**
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     * @param PaymentProviderFactory|\PhpSpec\Wrapper\Collaborator $paymentProviderFactory
     * @param PaymentProvider|\PhpSpec\Wrapper\Collaborator $paymentProvider
     * @throws \Exception
     */
    function it_handles_process_transaction_commands(
        TransactionRepository $transactionRepository,
        PaymentProviderFactory $paymentProviderFactory,
        PaymentProvider $paymentProvider
    ) {
        $transaction1 = $this->createConfirmedTransaction();
        $transaction2 = $this->createConfirmedTransaction();

        $transactions = [
            $transaction1,
            $transaction2,
        ];

        $this->givenConfirmedTransactionsExist($transactionRepository, $transactions);
        $this->givenFactoryCreatesPaymentProviderForTransaction(
            $paymentProviderFactory,
            $paymentProvider,
            $transaction1
        );
        $this->givenFactoryCreatesPaymentProviderForTransaction(
            $paymentProviderFactory,
            $paymentProvider,
            $transaction2
        );

        $this->handle(null)->shouldBeEqualTo(2);

        $transactionRepository->add($transaction1)->shouldHaveBeenCalled();
        $transactionRepository->add($transaction2)->shouldHaveBeenCalled();
    }

    /**
     * @param TransactionRepository $transactionRepository
     * @throws \Exception
     */
    private function givenConfirmedTransactionsExist(TransactionRepository $transactionRepository, array $transactions)
    {
        $transactionRepository->findConfirmed(null)
            ->willReturn($transactions);
    }

    /**
     * @param PaymentProviderFactory $paymentProviderFactory
     * @param PaymentProvider $paymentProvider
     * @param Transaction $transaction
     */
    private function givenFactoryCreatesPaymentProviderForTransaction(
        PaymentProviderFactory $paymentProviderFactory,
        PaymentProvider $paymentProvider,
        Transaction $transaction
    ) {
        $paymentProviderFactory->create($transaction)->willReturn($paymentProvider);
    }

    /**
     * @return Transaction
     * @throws \Exception
     */
    private function createConfirmedTransaction(): Transaction
    {
        $transaction = new Transaction(
            new UserId(1),
            new Money('200', new Currency('EUR')),
            new Recipient('12345', 'John Doe'),
            'Transaction number one',
            111
        );
        $transaction->confirm(111);

        return $transaction;
    }
}
