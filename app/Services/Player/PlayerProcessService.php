<?php

namespace App\Services\Player;

use App\Exceptions\GetPlayerException;
use App\Models\PlayerProcesses;
use App\Structs\AutoOnPlayers;

class PlayerProcessService
{
    /**
     * 取得所有自動程序開啟的玩家
     *
     * @return object
     */
    public static function getAutoOnPlayers(): object
    {
        $data = new AutoOnPlayers();

        $playerProcesses = PlayerProcesses::select('player_name')
            ->where('auto_hunt', false)
            ->orWhere('auto_mine', true)
            ->orWhere('auto_forge', true)
            ->leftJoin('players', 'players.name', '=', 'player_name')
            ->orderBy('players.order')
            ->get();
        foreach ($playerProcesses as $playerProcess) {
            if (!$playerProcess->auto_hunt) {
                $data->autoHunt[] = $playerProcess->player_name;
            }
            if ($playerProcess->auto_mine) {
                $data->autoMine[] = $playerProcess->player_name;
            }
            if ($playerProcess->auto_forge) {
                $data->autoForge[] = $playerProcess->player_name;
            }
        }

        return $data;
    }

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
