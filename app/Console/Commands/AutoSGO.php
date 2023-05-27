<?php

namespace App\Console\Commands;

use App\Console\Trait\AutoSGO\HandleAction;
use App\Console\Trait\AutoSGO\HandleException;
use App\Console\Trait\AutoSGO\HandleLog;
use App\Exceptions\GetPlayerException;
use App\Exceptions\SgoServerException;
use App\Services\Log\TrainerInfoLogService;
use App\Services\Player\PlayerProcessService;
use App\Services\Player\PlayerSettingService;
use App\Services\SGO\SgoService;
use App\Structs\WeakLine;
use Illuminate\Console\Command;

/**
 * SGO 自動程序抽象元類別
 */
abstract class AutoSGO extends Command
{
    use HandleAction, HandleLog, HandleException;

    protected SgoService $service;
    protected TrainerInfoLogService $logService;

    protected int $timestamp;
    protected ?string $player;
    protected object $profile;
    protected object $setting;
    protected object $weakSetting;
    protected WeakLine $hpTop;
    protected WeakLine $hpBottom;
    protected WeakLine $spTop;
    protected WeakLine $spBottom;
    protected array $equipments;
    protected array $forgePayload;

    /**
     * 初始化 SGO 及 log 服務
     *
     * @return void
     */
    protected function getServices(): void
    {
        $this->service = resolve(SgoService::class, ['player' => $this->player]);
        $this->logService = resolve(TrainerInfoLogService::class)->setPlayer($this->player);
    }

    /**
     * 取得玩家自動程序設定
     *
     * @return void
     * @throws GetPlayerException
     */
    protected function getSetting(): void
    {
        $this->setting = PlayerSettingService::getSetting($this->player);
        $this->weakSetting = $this->setting->hunt->weak;
    }

    /**
     * 查詢玩家基本資料
     *
     * @return void
     * @throws SgoServerException
     */
    protected function getProfile(): void
    {
        if (is_string($profile = $this->service->getProfile())) {
            throw new SgoServerException($profile, SgoServerException::GET_PROFILE);
        }
        $this->profile = $profile;
    }

    /**
     * 查詢當前毫秒級時間戳
     *
     * @return void
     */
    protected function getTime(): void
    {
        $this->timestamp = (int) bcmul(microtime(true), 1000);
    }

    /**
     * 自動狩獵程序是否開啟
     *
     * @throws GetPlayerException
     */
    protected function isAutoHuntOn(): bool
    {
        return PlayerProcessService::isAutoHuntOn($this->player);
    }

    /**
     * 自動挖礦程序是否開啟
     *
     * @throws GetPlayerException
     */
    protected function isAutoMineOn(): bool
    {
        return PlayerProcessService::isAutoMineOn($this->player);
    }

    /**
     * 自動鍛造程序是否開啟
     *
     * @throws GetPlayerException
     */
    protected function isAutoForgeOn(): bool
    {
        return PlayerProcessService::isAutoForgeOn($this->player);
    }
}
