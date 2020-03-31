<?php

namespace PaymentsAPI\Infrastructure\Persistence\Doctrine\Type;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\IntegerType;
use PaymentsAPI\Domain\ValueObject\UserId;

/**
 * Class UserIdType
 * @package PaymentsAPI\Infrastructure\Persistence\Doctrine\Type
 */
class UserIdType extends IntegerType
{
    /**
     * @return string
     */
    public function getName(): string
    {
        return 'UserId';
    }

    /**
     * @param mixed $value
     * @param AbstractPlatform $platform
     * @return int
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform): int
    {
        if ($value instanceof UserId) {
            return $value->getId();
        }

        return $value;
    }

    /**
     * @param $value
     * @param AbstractPlatform $platform
     * @return UserId
     */
    public function convertToPHPValue($value, AbstractPlatform $platform): UserId
    {
        return new UserId($value);
    }
}