<?php

namespace App\Repositories;

use App\Models\Players;

class PlayerRepository
{
    public function getByName(string $name): ?Players
    {
        return Players::find($name);
    }
}
