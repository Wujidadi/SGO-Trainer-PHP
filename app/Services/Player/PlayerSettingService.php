<?php

namespace App\Services\Player;

use App\Exceptions\GetPlayerException;
use App\Models\PlayerSettings;

class PlayerSettingService
{
    /**
     * 取得玩家自動程序設定
     *
     * @param string $playerName 玩家暱稱
     * @return object
     * @throws GetPlayerException
     */
    public static function getSetting(string $playerName): object
    {
        if (empty($playerSettings = PlayerSettings::find($playerName))) {
            throw new GetPlayerException('Player setting not found');
        }
        return $playerSettings->setting;
    }
}
