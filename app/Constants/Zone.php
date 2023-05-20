<?php

namespace App\Constants;

class Zone
{
    public const MAP = [
        0 => '起始之鎮',
        1 => '大草原',
        1001 => '草原秘徑',
        2 => '猛牛原',
        2001 => '被詛咒的寺院',
        3 => '兒童樂園',
        4 => '蘑菇園',
        4001 => '菇菇仙境',
        5 => '圓明園',
        6 => '非洲大草原',
        6001 => '神秘部落',
        7 => '空中花園',
        8 => '青藏高原',
        9 => '火鳳燎原',
        10 => '骷髏墓園',
        11 => '鷹洞',
        12 => '蝙蝠洞',
        13 => '老鼠洞',
        14 => '岩洞',
        15 => '盤絲洞',
    ];

    public const HIDDEN = [
        1001 => 1,
        2001 => 2,
        4001 => 4,
        6001 => 6,
    ];

    public const ENTRY = [
        1001 => 16,
        2001 => 18,
        4001 => 12,
        6001 => 14,
    ];

    public static function getBaseMap(int $zone): array
    {
        if (in_array($zone, array_keys(self::HIDDEN))) {
            return [
                'id' => self::HIDDEN[$zone],
                'name' => self::MAP[self::HIDDEN[$zone]],
            ];
        }
        return [
            'id' => $zone,
            'name' => self::MAP[$zone],
        ];
    }
}
