<?php

namespace PaymentsAPI\Infrastructure\Persistence\Doctrine\Fixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Money\Currency;
use Money\Money;
use PaymentsAPI\Domain\Factory\TransactionFactory;
use PaymentsAPI\Domain\Repository\TransactionRepository;
use PaymentsAPI\Domain\ValueObject\Recipient;
use PaymentsAPI\Domain\ValueObject\UserId;

/**
 * Class TransactionsFixtureLoader
 * @package QuotesAPI\Infrastructure\Persistence\Doctrine\Fixtures
 */
class TransactionsFixtureLoader extends Fixture
{
    /**
     * @var TransactionRepository
     */
    private $transactionRepository;

    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    /**
     * TransactionsFixtureLoader constructor.
     * @param TransactionRepository $transactionRepository
     * @param TransactionFactory $transactionFactory
     */
    public function __construct(TransactionRepository $transactionRepository, TransactionFactory $transactionFactory)
    {
        $this->transactionRepository = $transactionRepository;
        $this->transactionFactory = $transactionFactory;
    }

    /**
     * @param ObjectManager $manager
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $moneyEur = new Money('2000', new Currency('EUR'));
        $moneyUsd = new Money('2000', new Currency('USD'));
        $recipient = new Recipient('12345', 'John Doe');
        $details = 'Transaction number one';
        $userId = new UserId(1);

        // An unconfirmed payment.
        $transaction1 = $this->transactionFactory->create($moneyEur, $userId, $recipient, $details);

        $this->transactionRepository->add($transaction1);

        // An unconfirmed payment.
        $transaction2 = $this->transactionFactory->create($moneyEur, $userId, $recipient, $details);

        $this->transactionRepository->add($transaction2);

        // A confirmed eur payment.
        $transaction3 = $this->transactionFactory->create($moneyEur, $userId, $recipient, $details);
        $transaction3->confirm(111);

        $this->transactionRepository->add($transaction3);

        // A confirmed usd payment.
        $transaction4 = $this->transactionFactory->create($moneyUsd, $userId, $recipient, $details);
        $transaction4->confirm(111);

        $this->transactionRepository->add($transaction4);

        // A completed payment.
        $transaction5 = $this->transactionFactory->create($moneyEur, $userId, $recipient, $details);
        $transaction5->complete();

        $this->transactionRepository->add($transaction5);
    }
}