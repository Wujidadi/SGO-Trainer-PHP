<?php

namespace App\Utilities\Log;

use Wujidadi\LogFacade\LogFacade as Facade;
use Wujidadi\LogFacade\Logger;

class LogFacade extends Facade
{
    public static function sgo(): Logger
    {
        return new Logger('sgo');
    }

    public static function trainer(): Logger
    {
        return new Logger('trainer');
    }
}
