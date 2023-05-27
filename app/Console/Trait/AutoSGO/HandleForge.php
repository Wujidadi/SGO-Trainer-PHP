<?php

namespace App\Console\Trait\AutoSGO;

use App\Constants\Equipment;
use App\Exceptions\DbLogException;
use App\Exceptions\NullMineException;
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
     * @throws NullMineException
     */
    protected function handleForge(): void
    {
        if ($this->profile->zoneName === '起始之鎮') {
            $this->convertForgePayload();
            $this->forge();
            $this->logForge();
        } else {
            $this->logCantForge();
        }
    }

    /**
     * 將以礦物名稱為鍵值的鍛造 payload，轉為 API 所需的、以 ID 為鍵值
     *
     * @return void
     * @throws NullMineException
     */
    protected function convertForgePayload(): void
    {
        $itemMap = $this->service->getItemMap();
        $rawPayload = $this->setting->forge;
        $this->forgePayload = [
            'equipmentName' => $rawPayload['equipmentName'],
            'selected' => [],
            'type' => $rawPayload['type'],
        ];
        foreach ($rawPayload['selected'] as $item) {
            $mine = $itemMap->mines->{$item['name']};
            if (!$mine) {
                throw new NullMineException("素材{$item['name']}不存在");
            }
            $this->forgePayload['selected'][] = [
                'id' => $itemMap->mines->{$item['name']}->id,
                'quantity' => $item['quantity'],
            ];
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
