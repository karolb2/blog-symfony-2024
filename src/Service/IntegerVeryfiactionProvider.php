<?php

declare(strict_types=1);

namespace App\Service;

class IntegerVeryfiactionProvider
{
    public function isNumeric(string|int $value): bool
    {
        if(!is_numeric($value))
            throw new \Exception('ID must be an integer');

        return true;
    }
}
