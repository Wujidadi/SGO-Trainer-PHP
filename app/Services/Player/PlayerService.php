<?php

namespace App\Services\Player;

use App\Exceptions\GetPlayerException;
use App\Models\Players;

class PlayerService
{
    /**
     * @throws GetPlayerException
     */
    public function getToken(string $playerName): string
    {
        if (empty($player = Players::find($playerName))) {
            throw new GetPlayerException('Player not found');
        }
        return $player->token;
    }
}
