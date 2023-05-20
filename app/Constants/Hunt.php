<?php

namespace App\Constants;

class Hunt
{
    public const TYPE = [
        'normal' => 1,
        'rush' => 2,
    ];

    public const DESCRIPTION = [
        1 => '狩獵',
        2 => '趕路',
    ];

    /**
     * 冷卻時間
     *
     * 此冷卻時間為 60 秒減去真正冷卻時間的剩餘毫秒數
     *
     * @var array<string, int>
     */
    public const COOLDOWN = [
        'hunt' => 50000,
        'attack' => 0,
    ];
}
