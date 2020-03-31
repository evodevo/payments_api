<?php

namespace PaymentsAPI\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;
use PaymentsAPI\Domain\ValueObject\TransactionId;

/**
 * Class TransactionIdType
 * @package PaymentsAPI\Infrastructure\Persistence\Doctrine\Type
 */
class TransactionIdType extends IntegerType
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'TransactionId';
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return int
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): int
    {
        if ($value instanceof TransactionId) {
            return $value->getId();
        }

        return $value;
    }

    /**
     * @param $value
     * @param AbstractPlatform $platform
     * @return TransactionId
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): TransactionId
    {
        return new TransactionId($value);
    }
}