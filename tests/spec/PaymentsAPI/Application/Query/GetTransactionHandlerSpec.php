<?php

namespace spec\PaymentsAPI\Application\Query;

use PaymentsAPI\Application\DataTransformer\TransactionTransformer;
use PaymentsAPI\Application\Query\GetTransaction;
use PaymentsAPI\Application\Query\GetTransactionHandler;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\ValueObject\TransactionId;
use PhpSpec\ObjectBehavior;

/**
 * Class GetTransactionHandlerSpec
 * @package spec\PaymentsAPI\Application\Query
 */
class GetTransactionHandlerSpec extends ObjectBehavior
{
    /**
     * @param TransactionRepository $transactionRepository
     * @param TransactionTransformer $transactionTransformer
     */
    function let(TransactionRepository $transactionRepository, TransactionTransformer $transactionTransformer)
    {
        $this->beConstructedWith($transactionRepository, $transactionTransformer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(GetTransactionHandler::class);
    }

    /**
     * @param TransactionRepository|\PhpSpec\Wrapper\Collaborator $transactionRepository
     * @param Transaction|\PhpSpec\Wrapper\Collaborator $transaction
     * @param TransactionTransformer|\PhpSpec\Wrapper\Collaborator $transactionTransformer
     * @throws \Doctrine\ORM\EntityNotFoundException
     */
    function it_handles_get_transaction_query(
        TransactionRepository $transactionRepository,
        Transaction $transaction,
        TransactionTransformer $transactionTransformer
    ) {
        $transactionData = $this->createTransactionDataArray();

        $this->givenTransactionExists($transactionRepository, $transaction);
        $this->givenTransformerTransformsTransaction($transactionTransformer, $transaction, $transactionData);

        $this->handle(new GetTransaction(1))->shouldReturn($transactionData);
    }

    /**
     * @param TransactionRepository $transactionRepository
     * @param Transaction $transaction
     */
    private function givenTransactionExists(TransactionRepository $transactionRepository, Transaction $transaction)
    {
        $transactionRepository->find(new TransactionId(1))->willReturn($transaction);
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
