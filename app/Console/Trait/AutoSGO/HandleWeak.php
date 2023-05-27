<?php

namespace App\Console\Trait\AutoSGO;

use App\Exceptions\DbLogException;
use App\Exceptions\SgoServerException;
use App\Structs\WeakLine;
use App\Utilities\Helpers\DataHelper;

/**
 * 處理衰弱狀態 (低 HP、SP 時吃藥或休息)
 *
 * @used-by \App\Console\Commands\AutoHunt
 */
trait HandleWeak
{
    /**
     * 代入參數，計算 HP 或 SP 休整上/下限
     *
     * @param string $pointType hp 或 sp
     * @param string $reType rest 或 replenish
     * @param string $borderType top 或 bottom
     * @return void
     */
    private function calculateRePoint(string $pointType, string $reType, string $borderType): void
    {
        $border = $pointType . ucfirst($borderType); // hpTop, spTop, bpBottom, spBottom
        $fullKey = 'full' . ucfirst($pointType); // fullHp, fullSp

        $config = @$this->weakSetting->$pointType->$reType->$borderType; // ex: $this->>weakSetting->hp->rest->top
        $fullValue = $this->profile->$fullKey; // ex: $this->profile->fullHp

        if (empty($this->$border)) {
            $this->$border = resolve(WeakLine::class);
        }

        // 設定值不存在
        if (empty($config)) {
            if ($borderType == 'top') {
                // 上限：fullHp or fullSp
                $this->$border->$reType = $fullValue;
            } else {
                // 下限：fullHp or fullSp * stop_rest or stop_replenish
                $this->$border->$reType = $fullValue * config('app.sgo.stop_' . $reType);
            }
            return;
        }

        if (DataHelper::isPercentage($config)) {
            // 設定值為百分比形式
            $this->$border->$reType = (int) round($fullValue * floatval($config) / 100);
        } else {
            // 設定值為一般數字
            $this->$border->$reType = (int) $config;
        }
    }

    /**
     * 計算 HP 及 SP 休整上限
     *
     * @return void
     */
    protected function calculateTop(): void
    {
        $this->calculateRePoint('hp', 'rest', 'top');
        $this->calculateRePoint('hp', 'replenish', 'top');
        $this->calculateRePoint('sp', 'rest', 'top');
        $this->calculateRePoint('sp', 'replenish', 'top');
    }

    /**
     * 計算 HP 及 SP 休整下限
     *
     * @return void
     */
    protected function calculateBottom(): void
    {
        $this->calculateRePoint('hp', 'rest', 'bottom');
        $this->calculateRePoint('hp', 'replenish', 'bottom');
        $this->calculateRePoint('sp', 'rest', 'bottom');
        $this->calculateRePoint('sp', 'replenish', 'bottom');
    }

    /**
     * 是否處於衰弱狀態 (HP 或 SP 低於休整下限)
     *
     * @return bool
     */
    protected function isWeak(): bool
    {
        return $this->lowHp() || $this->lowSp();
    }

    /**
     * HP 是否低於休整下限
     *
     * @return bool
     */
    protected function lowHp(): bool
    {
        if ($this->weakSetting->replenish) {
            return $this->profile->hp < $this->hpBottom->replenish;
        }
        return $this->profile->hp < $this->hpBottom->rest;
    }

    /**
     * SP 是否低於休整下限
     *
     * @return bool
     */
    protected function lowSp(): bool
    {
        if ($this->weakSetting->replenish) {
            return $this->profile->sp < $this->spBottom->replenish;
        }
        return $this->profile->sp < $this->spBottom->rest;
    }

    /**
     * 吃藥
     *
     * @param string $type hp 或 sp
     * @throws SgoServerException
     * @throws DbLogException
     */
    protected function takeMedicine(string $type): void
    {
        if (empty($this->weakSetting->medicine) || empty($this->weakSetting->medicine->$type)) {
            return;
        }

        $bottom = "{$type}Bottom"; // hpBottom, spBottom
        $useCount = 0;

        $medicines = $this->service->getConsumablesByNames($this->weakSetting->medicine->$type->item);
        while (count($medicines) > 0 && ($this->profile->$type < $this->$bottom->replenish)) {
            foreach ($medicines as $itemName => $medicine) {
                if ($medicine->quantity <= 0) {
                    unset($medicines[$itemName]);
                    continue;
                }
                $response = $this->useItem($medicine->id, $this->weakSetting->medicine->$type->quantity);
                $useCount++; // 每吃 1 次補品，計數加 1
                $this->logUseItem($itemName, $this->weakSetting->medicine->$type->quantity);
                $this->profile = $response->profile;
            }
            $this->getTime();
        }

        // 完全沒吃到補品，代表指定的補品已吃完，但仍然處在衰弱狀態，只好休息
        if ($useCount <= 0) {
            $this->rest();
            $this->logRest();
        }
    }
}
