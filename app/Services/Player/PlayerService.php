<?php

namespace App\Services\Player;

use App\Exceptions\GetPlayerException;
use App\Repositories\PlayerRepository;

class PlayerService
{
    /**
     * @throws GetPlayerException
     */
    public function getToken(string $playerName): string
    {
        if (empty($player = resolve(PlayerRepository::class)->getByName($playerName))) {
            throw new GetPlayerException('Player not found');
        }
        return $player->token;
    }
}
