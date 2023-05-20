<?php

namespace App\Services\Player;

use App\Exceptions\GetPlayerException;
use App\Models\Players;

class PlayerService
{
    /**
     * 取得玩家 token
     *
     * @param string $playerName 玩家暱稱
     * @return string
     * @throws GetPlayerException
     */
    public static function getToken(string $playerName): string
    {
        if (empty($player = Players::find($playerName))) {
            throw new GetPlayerException('Player not found');
        }
        return $player->token;
    }
}
