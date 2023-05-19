<?php

namespace App\Console\Commands;

use App\Services\SGO\SgoService;
use Illuminate\Console\Command;

class AutoHunt extends Command
{
    protected $signature = 'sgo:auto-hunt {--player=}';
    protected $description = '自動狩獵';

    protected SgoService $service;
    protected ?string $player;

    public function handle(): void
    {
        $this->player = $this->option('player');
        if (!$this->player) {
            return;
        }

        $this->service = resolve(SgoService::class, ['player' => $this->player]);
    }
}
