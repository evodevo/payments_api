<?php

declare(strict_types=1);

namespace PaymentsAPI\Domain\Service;

/**
 * Interface CodeGenerator
 * @package PaymentsAPI\Domain\Service
 */
interface CodeGenerator
{
    /**
     * @return int
     */
    public function generate(): int;
}