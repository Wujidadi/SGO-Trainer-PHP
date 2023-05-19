<?php

namespace App\Utilities;

use Wujidadi\LogFacade\LogFacade as Facade;
use Wujidadi\LogFacade\Logger;

class LogFacade extends Facade
{
    public static function sgo(): Logger
    {
        return new Logger('sgo');
    }
}
