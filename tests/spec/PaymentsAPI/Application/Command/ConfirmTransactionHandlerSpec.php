<?php

namespace spec\PaymentsAPI\Application\Command;

use PaymentsAPI\Application\Command\ConfirmTransaction;
use PaymentsAPI\Application\Command\ConfirmTransactionHandler;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\ValueObject\TransactionId;
use PhpSpec\ObjectBehavior;

/**
 * Class ConfirmTransactionHandlerSpec
 * @package spec\PaymentsAPI\Application\Command
 */
class ConfirmTransactionHandlerSpec extends ObjectBehavior
{
    /**
     * @param TransactionRepository $transactionRepository
     */
    function let(TransactionRepository $transactionRepository)
    {
        $this->beConstructedWith($transactionRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ConfirmTransactionHandler::class);
    }

    /**
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     * @param Transaction|\PhpSpec\Wrapper\Collaborator $transaction
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    function it_handles_confirm_transaction_commands(
        TransactionRepository $transactionRepository,
        Transaction $transaction
    ) {
        $this->givenTransactionExists($transactionRepository, $transaction);

        $this->handle(
            new ConfirmTransaction(1, 111)
        );

        $transactionRepository->add($transaction)->shouldHaveBeenCalled();
    }

    /**
     * @param TransactionRepository $transactionRepository
     * @param Transaction $transaction
     */
    private function givenTransactionExists(TransactionRepository $transactionRepository, Transaction $transaction)
    {
        $transactionRepository->find(new TransactionId(1))
            ->willReturn($transaction);
    }
}
