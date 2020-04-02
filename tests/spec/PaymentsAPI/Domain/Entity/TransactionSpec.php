<?php

namespace spec\PaymentsAPI\Domain\Entity;

use Money\Currency;
use Money\Money;
use PaymentsAPI\Domain\Entity\Transaction;
use PaymentsAPI\Domain\Exception\ConfirmationFailed;
use PaymentsAPI\Domain\Exception\InvalidConfirmationCode;
use PaymentsAPI\Domain\ValueObject\Recipient;
use PaymentsAPI\Domain\ValueObject\UserId;
use PhpSpec\ObjectBehavior;

/**
 * Class TransactionSpec
 * @package spec\PaymentsAPI\Domain\Entity
 */
class TransactionSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(
            new UserId(1),
            new Money('1000', new Currency('EUR')),
            new Recipient('12345', 'John Doe'),
            'Transaction number one',
            111
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Transaction::class);
    }

    function it_has_correct_initial_status()
    {
        $this->getStatus()->shouldBe(Transaction::STATUS_CREATED);
    }

    function it_can_be_confirmed_with_valid_code()
    {
        $this->confirm(111);

        $this->getStatus()->shouldBe(Transaction::STATUS_CONFIRMED);
    }

    function it_throws_exception_when_confirming_with_invalid_code()
    {
        $this->shouldThrow(InvalidConfirmationCode::class)->duringConfirm(123);
    }

    function it_throws_exception_when_already_confirmed()
    {
        $this->confirm(111);

        $this->shouldThrow(ConfirmationFailed::class)->duringConfirm(111);
    }

    function it_can_be_completed()
    {
        $this->confirm(111);
        $this->complete();

        $this->getStatus()->shouldBe(Transaction::STATUS_COMPLETED);
    }

    function it_can_update_details()
    {
        $this->updateDetails('Transaction number one updated');

        $this->getDetails()->shouldBe('Transaction number one updated');
    }

    function it_can_apply_fee()
    {
        $eur = new Currency('EUR');
        $money = new Money('200', $eur);
        $total = new Money('1200', $eur);

        $this->applyFee($money);

        $this->getFee()->equals($money)->shouldBe(true);
        $this->getTotal()->equals($total)->shouldBe(true);
    }

    function it_exceeds_amount()
    {
        $this->exceedsAmount(new Money('900', new Currency('EUR')))->shouldBe(true);
    }

    function it_does_not_exceed_amount()
    {
        $this->exceedsAmount(new Money('1100', new Currency('EUR')))->shouldBe(false);
    }

    function it_has_currency()
    {
        $this->hasCurrency(new Currency('EUR'))->shouldBe(true);
    }

    function it_does_not_have_currency()
    {
        $this->hasCurrency(new Currency('USD'))->shouldBe(false);
    }
}
