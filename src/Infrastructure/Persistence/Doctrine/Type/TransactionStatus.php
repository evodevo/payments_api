<?php

namespace PaymentsAPI\Infrastructure\Persistence\Doctrine\Type;

/**
 * Class TransactionStatus
 * @package PaymentsAPI\Infrastructure\Persistence\Doctrine\Type
 */
class TransactionStatus extends EnumType
{
    /**
     * @var string
     */
    protected $name = 'TransactionStatus';

    /**
     * @var array
     */
    protected $values = ['created', 'confirmed', 'completed', 'failed'];
}