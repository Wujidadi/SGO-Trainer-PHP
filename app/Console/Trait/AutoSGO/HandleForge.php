<?php

namespace App\Console\Trait\AutoSGO;

use App\Constants\Equipment;
use App\Exceptions\DbLogException;
use App\Exceptions\SgoServerException;

/**
 * 處理鍛造事宜
 *
 * @used-by \App\Console\Commands\AutoForge
 */
trait HandleForge
{
    /**
     * 處理鍛造
     *
     * @return void
     * @throws SgoServerException
     * @throws DbLogException
     */
    protected function handleForge(): void
    {
        if ($this->profile->zoneName === '起始之鎮') {
            $this->forge();
            $this->logForge();
        } else {
            $this->logCantForge();
        }
    }

    /**
     * 是否可完成鍛造
     *
     * @return bool
     */
    protected function canCompleteForge(): bool
    {
        return isset($this->profile->forgingCompletionTime) && $this->profile->forgingCompletionTime <= $this->timestamp;
    }

    /**
     * 鍛造設定是否「不」滿足 API 所需條件
     *
     * @return bool
     */
    protected function isForgePayloadNotFilled(): bool
    {
        if (!isset($this->setting->forge)) {
            return true;
        }
        if (!isset($this->setting->forge->equipmentName)) {
            return true;
        }
        if (!isset($this->setting->forge->selected)) {
            return true;
        }
        if (!is_array($this->setting->forge->selected) || empty($this->setting->forge->selected)) {
            return true;
        }
        if (!isset($this->setting->forge->type)) {
            return true;
        }
        if (!in_array($this->setting->forge->type, array_keys(Equipment::TYPE))) {
            return true;
        }
        return false;
    }
}
