<?php

namespace App\Utilities\Helpers;

class DataHelper
{
    public static function isPercentage(string|int $value): bool
    {
        return preg_match('/^\d+\.?\d*%$/', $value);
    }
}
