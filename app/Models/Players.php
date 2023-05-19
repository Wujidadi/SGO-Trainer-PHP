<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 玩家基本資料模型
 *
 * @property string $name 玩家暱稱
 * @property string $token Token
 * @property int $order 排序
 * @property ?Carbon $created_at 建立時間
 * @property ?Carbon $updated_at 更新時間
 * @property-read PlayerProcesses $process 玩家自動程序開關
 * @property-read PlayerItems $items 玩家物品清單
 * @property-read PlayerSettings $settings 玩家自動程序設定
 */
class Players extends Model
{
    protected $table = 'players';

    protected $primaryKey = 'name';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name',
        'token',
        'order',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime:Y-m-d H:i:s.u',
        'updated_at' => 'datetime:Y-m-d H:i:s.u',
    ];

    public function process(): HasOne
    {
        return $this->hasOne(PlayerProcesses::class, 'player_name', 'name')->withDefault();
    }

    public function items(): HasOne
    {
        return $this->hasOne(PlayerItems::class, 'player_name', 'name')->withDefault();
    }

    public function settings(): HasOne
    {
        return $this->hasOne(PlayerSettings::class, 'player_name', 'name')->withDefault();
    }
}
