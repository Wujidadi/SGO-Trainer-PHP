<?php

namespace App\Console\Trait\AutoSGO;

use App\Constants\Hunt;
use App\Constants\Zone;
use App\Exceptions\DbLogException;
use App\Exceptions\SgoServerException;

/**
 * 處理狩獵事宜
 *
 * @package App\Console\Trait
 */
trait HandleHunt
{
    /**
     * 處理狩獵
     *
     * @throws SgoServerException
     * @throws DbLogException
     */
    protected function handleHunt(): void
    {
        $this->getProfile();

        $this->handleZone();

        // 往目標地圖移動
        if ($this->profile->zoneName != Zone::MAP[$this->zone]) {
            $this->move($this->zone);
            $this->logMove(Zone::getBaseMap($this->zone)['name']);
            return;
        }

        // 回城
        if ($this->profile->huntStage > $this->stage) {
            $this->goHome();
            $this->logMove(Zone::MAP[0]);
            return;
        }

        // HP 或 SP 不足
        if ($this->isWeak()) {
            if ($this->weakSetting->replenish) {
                if ($this->lowHp()) {
                    $this->takeMedicine('hp');
                }
                if ($this->lowSp()) {
                    $this->takeMedicine('sp');
                }
            } else {
                // 休息
                $this->rest();
                $this->logRest();
                return;
            }
        }

        // 狩獵
        $huntType = Hunt::TYPE['normal'];
        // 判斷是否趕路
        if ($this->rush && $this->profile->huntStage < $this->rush) {
            $huntType = Hunt::TYPE['rush'];
        }
        $this->hunt($huntType);
        $this->logHunt(Hunt::DESCRIPTION[$huntType]);
    }

    /**
     * 處理狩獵區域
     *
     * @return void
     * @throws SgoServerException
     * @throws DbLogException
     */
    protected function handleZone(): void
    {
        // 狩獵目標區域為秘徑
        if (in_array($this->zone, array_keys(Zone::HIDDEN))) {
            // 不在秘徑主地圖
            if ($this->profile->huntZone != Zone::HIDDEN[$this->zone]) {
                $this->zone = Zone::HIDDEN[$this->zone];
                return;
            }
            // 已在秘徑
            if ($this->profile->zoneName == Zone::MAP[$this->zone]) {
                // 已超過最大樓層，直接返回主地圖的第 1 層（不必先回起始之鎮）
                if ($this->profile->huntStage > $this->stage) {
                    $this->zone = Zone::HIDDEN[$this->zone];
                }
                return;
            }
            // 未到秘徑入口樓層
            if ($this->profile->huntStage < Zone::ENTRY[$this->zone]) {
                $this->zone = Zone::HIDDEN[$this->zone];
                return;
            }
            // 已到秘徑入口樓層
            if ($this->profile->huntStage == Zone::ENTRY[$this->zone]) {
                $this->profile = $this->path($this->zone)->profile;
                $this->logPath(Zone::MAP[$this->zone]);
                return;
            }
            // 已過秘徑入口樓層但尚未進入，也無法直接返回主地圖 1 樓，只能直接回城
            $this->zone = 0;
        }
    }
}
