<?php

namespace App\Services\SGO;

use App\Services\SGO\Api as SgoApi;
use App\Structs\ItemMap;

class SgoService extends SgoApi
{
    /**
     * 回城
     *
     * @return object|string
     */
    public function goHome(): object|string
    {
        return $this->move(0);
    }

    /**
     * 取得除武器裝備外的物品清單，以物品名稱為鍵值
     *
     * @return ItemMap
     */
    public function getItemMap(): ItemMap
    {
        $map = resolve(ItemMap::class);

        $items = $this->getItems();
        $types = ['mines', 'consumables'];
        foreach ($types as $type) {
            foreach ($items->$type as $item) {
                $map->$type->{$item->name} = $item;
            }
        }

        return $map;
    }

    /**
     * 依物品名稱取得 ID 及數量
     *
     * @param array $names
     * @return array
     */
    public function getConsumablesByNames(array $names): array
    {
        $medicine = [];
        $consumables = $this->getItems('consumables');
        foreach ($consumables as $item) {
            if (in_array($item->name, $names)) {
                $medicine[$item->name] = (object) [
                    'id' => $item->id,
                    'quantity' => $item->available,
                ];
            }
        }
        return $medicine;
    }
}
