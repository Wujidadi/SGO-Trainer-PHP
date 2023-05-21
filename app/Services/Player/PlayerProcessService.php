<?php

namespace App\Services\Player;

use App\Exceptions\GetPlayerException;
use App\Models\PlayerProcesses;

class PlayerProcessService
{
    /**
     * 玩家自動狩獵是否開啟
     *
     * @param string $playerName 玩家暱稱
     * @return bool
     * @throws GetPlayerException
     */
    public static function isAutoHuntOn(string $playerName): bool
    {
        if (empty($playerProcesses = PlayerProcesses::find($playerName))) {
            throw new GetPlayerException('Player process not found');
        }
        return $playerProcesses->auto_hunt;
    }

    /**
     * 玩家自動挖礦是否開啟
     *
     * @param string $playerName 玩家暱稱
     * @return bool
     * @throws GetPlayerException
     */
    public static function isAutoMineOn(string $playerName): bool
    {
        if (empty($playerProcesses = PlayerProcesses::find($playerName))) {
            throw new GetPlayerException('Player process not found');
        }
        return $playerProcesses->auto_mine;
    }

    /**
     * 玩家自動鍛造是否開啟
     *
     * @param string $playerName 玩家暱稱
     * @return bool
     * @throws GetPlayerException
     */
    public static function isAutoForgeOn(string $playerName): bool
    {
        if (empty($playerProcesses = PlayerProcesses::find($playerName))) {
            throw new GetPlayerException('Player process not found');
        }
        return $playerProcesses->auto_forge;
    }
}
