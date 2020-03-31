<?php

declare(strict_types=1);

namespace PaymentsAPI\Infrastructure\Service\CodeGenerator;

use PaymentsAPI\Domain\Service\CodeGenerator;

/**
 * Class StubGenerator
 * @package PaymentsAPI\Infrastructure\Service\PaymentProvider
 */
class StubGenerator implements CodeGenerator
{
    /**
     * @return int
     */
    public function generate(): int
    {
        return 111;
    }
}