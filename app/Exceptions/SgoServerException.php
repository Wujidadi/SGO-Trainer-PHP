<?php

namespace App\Exceptions;

use Exception;

class SgoServerException extends Exception
{
    public const GENERAL = 1;
    public const GET_PROFILE = 2;
}
