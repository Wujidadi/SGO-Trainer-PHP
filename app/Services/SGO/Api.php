<?php

namespace App\Services\SGO;

use App\Exceptions\GetPlayerException;
use App\Services\SGO\Client as SgoClient;

class Api
{
    private SgoClient $client;

    protected string $playerName;

    /**
     * @throws GetPlayerException
     */
    public function __construct(string $player)
    {
        $this->playerName = $player;
        $this->client = new SgoClient($this->playerName);
    }

    /**
     * 取得玩家當前個人基本資訊
     *
     * @return object|string
     */
    public function getProfile(): object|string
    {
        return $this->client->get('/api/profile');
    }

    /**
     * 分配屬性
     *
     * @param array $points
     * @return object|string
     */
    public function addPoints(array $points): object|string
    {
        return $this->client->post('/api/addPoints', $points);
    }

    /**
     * 重生
     *
     * @return object|string
     */
    public function revive(): object|string
    {
        return $this->client->post('/api/action/revive');
    }

    /**
     * 休息
     *
     * @return object|string
     */
    public function rest(): object|string
    {
        return $this->client->post('/api/action/rest');
    }

    /**
     * 完成行動
     *
     * @return object|string
     */
    public function completeAction(): object|string
    {
        return $this->client->post('/api/action/complete');
    }

    /**
     * 移動
     *
     * @param int $zone
     * @return object|string
     */
    public function move(int $zone): object|string
    {
        return $this->client->post("/api/zone/move/$zone");
    }

    /**
     * 完成移動
     *
     * @return object|string
     */
    public function completeMove(): object|string
    {
        return $this->client->post('/api/zone/move/complete');
    }

    /**
     * 取得目前裝備和技能
     *
     * @return object|string
     */
    public function getHuntInfo(): object|string
    {
        return $this->client->get('/api/hunt/info');
    }

    /**
     * 著裝
     *
     * @param int $equipmentId
     * @return object|string
     */
    public function equip(int $equipmentId): object|string
    {
        return $this->client->post("/api/equipment/$equipmentId/equip");
    }

    /**
     * 卸裝
     *
     * @param int $equipmentId
     * @return object|string
     */
    public function unequip(int $equipmentId): object|string
    {
        return $this->client->post("/api/equipment/$equipmentId/unequip");
    }

    /**
     * 狩獵
     *
     * @param int $type
     * @return object|string
     */
    public function hunt(int $type = 1): object|string
    {
        return $this->client->post('/api/hunt', ['type' => $type]);
    }

    /**
     * 進入岔路
     *
     * @param int $pathId
     * @return object|string
     */
    public function path(int $pathId): object|string
    {
        return $this->client->post('/api/path', ['pathId' => $pathId]);
    }

    /**
     * 取得物品清單
     *
     * @param string|null $type
     * @return object|array
     */
    public function getItems(?string $type = null): object|array
    {
        $items = $this->client->get('/api/items');
        if (is_null($type)) {
            return $items;
        }
        return $items->$type ?? [];
    }

    /**
     * 設定裝備顏色
     *
     * @param int $equipmentId
     * @param string $color
     * @return object|string
     */
    public function setEquipmentColor(int $equipmentId, string $color): object|string
    {
        return $this->client->post("/api/equipment/$equipmentId/color", [
            'color' => $color,
        ]);
    }

    /**
     * 使用物品
     *
     * @param int $id
     * @param int $quantity
     * @return object|string
     */
    public function useItem(int $id, int $quantity): object|string
    {
        return $this->client->post("/api/items/$id/use", [
            'quantity' => $quantity,
        ]);
    }

    /**
     * 鍛造
     *
     * @param array $data
     * @return object|string
     */
    public function forge(array $data): object|string
    {
        return $this->client->post('/api/forge', $data);
    }

    /**
     * 完成鍛造
     *
     * @return object|string
     */
    public function completeForge(): object|string
    {
        return $this->client->post('/api/forge/complete');
    }
}
