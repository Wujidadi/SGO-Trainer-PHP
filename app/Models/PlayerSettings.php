<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 玩家自動程序設定資料模型
 *
 * @property string $player_name 玩家暱稱
 * @property object $setting 設定
 * @property ?Carbon $created_at 建立時間
 * @property ?Carbon $updated_at 更新時間
 * @property-read Players $player 玩家基本資料
 */
class PlayerSettings extends Model
{
    protected $table = 'player_settings';

    protected $primaryKey = 'player_name';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'player_name',
        'setting',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'setting' => 'object',
        'created_at' => 'datetime:Y-m-d H:i:s.u',
        'updated_at' => 'datetime:Y-m-d H:i:s.u',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Players::class, 'player_name', 'name');
    }
}
