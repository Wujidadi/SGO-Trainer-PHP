<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * 玩家自動程序開關資料模型
 *
 * @property string $player_name 玩家暱稱
 * @property boolean $auto_hunt 自動狩獵
 * @property boolean $auto_mine 自動挖礦
 * @property boolean $auto_forge 自動鍛造
 * @property ?Carbon $created_at 建立時間
 * @property ?Carbon $updated_at 更新時間
 * @property-read Players $player 玩家基本資料
 * @property-read PlayerProcessLastActionTime $lastActionTime 玩家自動程序最後動作時間
 */
class PlayerProcesses extends Model
{
    protected $table = 'player_processes';

    protected $primaryKey = 'player_name';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'player_name',
        'auto_hunt',
        'auto_mine',
        'auto_forge',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $casts = [
        'auto_hunt' => 'boolean',
        'auto_mine' => 'boolean',
        'auto_forge' => 'boolean',
        'created_at' => 'datetime:Y-m-d H:i:s.u',
        'updated_at' => 'datetime:Y-m-d H:i:s.u',
    ];

    public function player(): BelongsTo
    {
        return $this->belongsTo(Players::class, 'player_name', 'name');
    }

    public function lastActionTime(): HasOne
    {
        return $this->hasOne(PlayerProcessLastActionTime::class, 'player_name', 'player_name')->withDefault();
    }
}
