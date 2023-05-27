<?php

namespace App\Console\Trait\AutoSGO;

use App\Constants\Equipment;
use App\Exceptions\DbLogException;

/**
 * 處理日誌
 *
 * @used-by \App\Console\Commands\AutoSGO
 */
trait HandleLog
{
    /**
     * 指定日誌分類
     *
     * @param string $category
     * @return static
     */
    private function setLogCategory(string $category): static
    {
        $this->logService->setCategory($category);
        return $this;
    }

    /**
     * 記錄日誌基底方法
     *
     * @param mixed ...$message
     * @return void
     * @throws DbLogException
     */
    private function log(mixed ...$message): void
    {
        $this->logService->write(...$message);
    }

    /**
     * 記錄休息日誌
     *
     * @param string|null $type hp 或 sp
     * @return void
     * @throws DbLogException
     */
    protected function logRest(?string $type = null): void
    {
        $this->setLogCategory('休息');
        if (is_null($type) || $this->profile->actionStatus !== '休息') {
            $this->log('開始休息');
            return;
        }
        $this->log('%s 未達設定下限，繼續休息', strtoupper($type));
    }

    /**
     * 記錄冷卻中日誌
     *
     * @return void
     * @throws DbLogException
     */
    protected function logCoolingDown(): void
    {
        $this->setLogCategory('冷卻')->log('冷卻中');
    }

    /**
     * 記錄行動中日誌
     *
     * @return void
     * @throws DbLogException
     */
    protected function logActing(): void
    {
        $this->setLogCategory($this->profile->actionStatus)
            ->log('%s中', $this->profile->actionStatus);
    }

    /**
     * 記錄行動完畢日誌
     *
     * @param object $response
     * @return void
     * @throws DbLogException
     */
    protected function logCompleteAction(object $response): void
    {
        if ($this->profile->actionStatus !== '休息') {
            $this->setLogCategory($this->profile->actionStatus)
                ->log('%s完畢', $this->profile->actionStatus);
            return;
        }

        $profile = $response->profile;
        $this->setLogCategory('休息')
            ->log('休息完畢，恢復了 %d 點 HP (%.2f%%) 與 %d (%.2f%%) 點 SP',
                $hpRecovered = $profile->hp - $this->profile->hp,
                round(($hpRecovered / $profile->fullHp) * 100, 2),
                $spRecovered = $profile->sp - $this->profile->sp,
                round(($spRecovered / $profile->fullSp) * 100, 2)
            );
    }

    /**
     * 記錄開始移動日誌
     *
     * @param string $mapName 樓層 (地圖) 名稱
     * @return void
     * @throws DbLogException
     */
    protected function logMove(string $mapName): void
    {
        $this->setLogCategory('移動')
            ->log('開始往%s移動', $mapName);
    }

    /**
     * 記錄移動完畢日誌
     *
     * @return void
     * @throws DbLogException
     */
    protected function logCompleteMove(): void
    {
        $this->setLogCategory('移動')
            ->log('移動完畢，到達%s', $this->profile->targetZoneName ?? '？？？');
    }

    /**
     * 記錄進入岔路日誌
     *
     * @param string $pathName 岔路地圖名稱
     * @return void
     * @throws DbLogException
     */
    protected function logPath(string $pathName): void
    {
        $this->setLogCategory('狩獵')
            ->log('進入%s', $pathName);
    }

    /**
     * 記錄狩獵日誌
     *
     * @param string $huntTypeDescription 狩獵類型描述字串，「狩獵」或「趕路」
     * @return void
     * @throws DbLogException
     */
    protected function logHunt(string $huntTypeDescription): void
    {
        $this->setLogCategory('狩獵')
            ->log('%s - %s %s', $huntTypeDescription, $this->profile->zoneName, $this->profile->huntStage);
    }

    /**
     * 記錄著裝日誌
     *
     * @param object $equipment
     * @return void
     * @throws DbLogException
     */
    protected function logEquip(object $equipment): void
    {
        $this->setLogCategory('裝備')
            ->log('穿上%s「%s」(攻擊 %d, 防禦 %d, 幸運 %d, 重量 %d, 耐久 %d)',
                $equipment->typeName,
                $equipment->name,
                $equipment->atk,
                $equipment->def,
                $equipment->lck,
                $equipment->wgt,
                $equipment->durability
            );
    }

    /**
     * 記錄卸裝日誌
     *
     * @param object $equipment
     * @return void
     * @throws DbLogException
     */
    protected function logUnequip(object $equipment): void
    {
        $this->setLogCategory('裝備')
            ->log('%s: 卸下%s「%s」(攻擊 %d, 防禦 %d, 幸運 %d, 重量 %d, 耐久 %d)',
                $equipment->typeName,
                $equipment->name,
                $equipment->atk,
                $equipment->def,
                $equipment->lck,
                $equipment->wgt,
                $equipment->durability
            );
    }

    /**
     * 記錄鍛造日誌
     *
     * @return void
     * @throws DbLogException
     */
    protected function logForge(): void
    {
        $this->setLogCategory('鍛造')
            ->log('開始鍛造%s「%s」',
                Equipment::TYPE[$this->setting->forge->type],
                $this->setting->forgeequipmentName
            );
    }

    /**
     * 記錄無法鍛造日誌
     *
     * @return void
     * @throws DbLogException
     */
    protected function logCantForge(): void
    {
        $this->setLogCategory('鍛造')
            ->log('位於 %s %s，無法鍛造',
                $this->profile->zoneName,
                $this->profile->huntStage
            );
    }

    /**
     * 記錄死亡日誌
     *
     * @return void
     * @throws DbLogException
     */
    protected function logDie(): void
    {
        $this->setLogCategory('死亡')
            ->log('死亡');
    }

    /**
     * 記錄開始重生日誌
     *
     * @return void
     * @throws DbLogException
     */
    protected function logRevive(): void
    {
        $this->setLogCategory('死亡')
            ->log('開始重生');
    }

    /**
     * 記錄使用補品日誌
     *
     * @param string $itemName 補品名稱
     * @param int $quantity 數量
     * @return void
     * @throws DbLogException
     */
    protected function logUseItem(string $itemName, int $quantity): void
    {
        $this->setLogCategory('吃藥')
            ->log('使用%s * %d', $itemName, $quantity);
    }
}
