<?php

namespace App\Console\Commands;

use App\Services\Player\PlayerProcessService;
use Illuminate\Console\Command;
use Laravel\Octane\Facades\Octane;

class AutoProcessEntry extends Command
{
    protected $signature = 'sgo:auto-process-entry';
    protected $description = '自動程序入口';

    protected const SLEEP = 11;

    public function handle(): void
    {
        $basePath = base_path();
        while (true) {
            $players = PlayerProcessService::getAutoOnPlayers();
            $autoHuntPlayers = $players->autoHunt;
            $autoMinePlayers = $players->autoMine;
            $autoForgePlayers = $players->autoForge;
            Octane::concurrently([
                function () use ($basePath, $autoHuntPlayers) {
                    foreach ($autoHuntPlayers as $player) {
                        shell_exec("cd $basePath && php artisan sgo:auto-hunt --player=$player > /dev/null 2>&1 &");
                    }
                },
                function () use ($basePath, $autoMinePlayers) {
                    foreach ($autoMinePlayers as $player) {
                        shell_exec("cd $basePath && php artisan sgo:auto-mine --player=$player > /dev/null 2>&1 &");
                    }
                },
                function () use ($basePath, $autoForgePlayers) {
                    foreach ($autoForgePlayers as $player) {
                        shell_exec("cd $basePath && php artisan sgo:auto-forge --player=$player > /dev/null 2>&1 &");
                    }
                },
            ]);
            sleep(self::SLEEP);
        }
    }
}
