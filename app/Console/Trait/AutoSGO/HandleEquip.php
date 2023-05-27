<?php

namespace App\Console\Trait\AutoSGO;

use App\Exceptions\DbLogException;
use App\Exceptions\SgoServerException;
use App\Exceptions\TrainerSettingException;

/**
 * 處理裝備相關事宜
 *
 * @used-by \App\Console\Commands\AutoHunt
 */
trait HandleEquip
{
    /**
     * 處理穿裝、卸裝、裝備回收等各種事項
     *
     * @return bool
     * @throws SgoServerException
     * @throws TrainerSettingException
     * @throws DbLogException
     */
    protected function handleEquipment(): bool
    {
        $this->unequipWornOut();
        return $this->reequip();
    }

    /**
     * 取得穿著中的的裝備列表
     *
     * @return void
     * @throws SgoServerException
     */
    protected function getEquipments(): void
    {
        $this->equipments = $this->getHuntInfo()->equipments;
    }

    /**
     * 卸除耐久過低的裝備
     *
     * @return void
     * @throws SgoServerException
     * @throws DbLogException
     */
    protected function unequipWornOut(): void
    {
        $this->getEquipments();

        foreach ($this->equipments as $equipment) {
            if ($equipment->durability < $this->setting->hunt->equipment->durability) {
                $this->unequip($equipment->id);
                $this->logUnequip($equipment);
            }
        }
    }

    /**
     * 自動著/卸裝
     *
     * @return bool
     * @throws TrainerSettingException
     * @throws SgoServerException
     * @throws DbLogException
     */
    protected function reequip(): bool
    {
        $this->getEquipments();

        $equipConf = $this->setting->hunt->equipment;
        if (!$equipConf->auto) {
            return true;
        }

        $nowEquipped = [];
        foreach ($this->equipments as $equipment) {
            $nowEquipped[] = $equipment->typeName;
        }

        $equipments = collect($this->service->getItems('equipments'));

        // 先不處理雙持
        foreach ($equipConf->type as $typeName => $typeConf) {
            // 跳過已裝備類型
            if (in_array($typeName, $nowEquipped)) {
                continue;
            }

            // 跳過關閉自動著/卸裝類型
            if (!$typeConf->auto) {
                continue;
            }

            // 依顏色和類型篩選裝備
            $filteredEquipments = $equipments->filter(function ($equipment) use ($typeName, $typeConf, $equipConf) {
                return $equipment->typeName === $typeName
                    && $equipment->color === $typeConf->color
                    && $equipment->durability >= $equipConf->durability;
            });

            // 數量為空時執行 After Run Out 策略
            if ($filteredEquipments->isEmpty()) {
                // 空手/缺裝繼續打
                if ($typeConf->afterRunOut == 0) {
                    continue;
                }
                // 原地閒置
                if ($equipConf->afterRunOut == 0) {
                    return false;
                }
                // 回城
                $this->goHome();
                return false;
            }

            // 裝備使用優先別策略
            $sortBy = [];
            $strategies = explode(',', $typeConf->strategy);
            foreach ($strategies as $strategy) {
                $sortOrder = match (substr($strategy, -1)) {
                    '+' => 'asc',
                    '-' => 'desc',
                    default => throw new TrainerSettingException('裝備優先別策略不正確'),
                };
                $field = substr($strategy, 0, -1);
                $sortBy[] = [$field, $sortOrder];
            }
            if (count($sortBy) > 0) {
                $filteredEquipments = $filteredEquipments->sortBy($sortBy);
            }

            // 穿裝
            $theEquipment = $filteredEquipments->first();
            $equipmentId = $theEquipment->id;
            $this->equip($equipmentId);
            $this->logEquip($theEquipment);
        }
        return true;
    }
}
