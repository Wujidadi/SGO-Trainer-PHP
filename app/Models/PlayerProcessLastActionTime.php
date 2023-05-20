<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * 玩家自動程序最後動作時間資料模型
 *
 * @property string $player_name 玩家暱稱
 * @property ?Carbon $created_at 建立時間
 * @property ?Carbon $last_auto_hunt_at 自動狩獵最後動作時間
 * @property ?Carbon $last_auto_mine_at 自動挖礦最後動作時間
 * @property ?Carbon $last_auto_forge_at 自動鍛造最後動作時間
 * @property-read PlayerProcesses $process 玩家自動程序開關
 * @property-read ?Carbon $autoHunt 自動狩獵最後動作時間
 * @property-read ?Carbon $autoMine 自動挖礦最後動作時間
 * @property-read ?Carbon $autoForge 自動鍛造最後動作時間
 */
class PlayerProcessLastActionTime extends Model
{
    protected $table = 'player_process_last_action_time';

    protected $primaryKey = 'player_name';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'player_name',
        'last_auto_hunt_at',
        'last_auto_mine_at',
        'last_auto_forge_at',
    ];

    protected $hidden = [
        'created_at',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:i:s.u',
        'last_auto_hunt_at' => 'datetime:Y-m-d H:i:s.u',
        'last_auto_mine_at' => 'datetime:Y-m-d H:i:s.u',
        'last_auto_forge_at' => 'datetime:Y-m-d H:i:s.u',
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(PlayerProcesses::class, 'player_name', 'player_name');
    }

    /**
     * @return Carbon|null $autoHunt
     */
    public function getAutoHuntAttribute(): ?Carbon
    {
        return $this->last_auto_hunt_at;
    }

    /**
     * @return Carbon|null $autoMine
     */
    public function getAutoMineAttribute(): ?Carbon
    {
        return $this->last_auto_mine_at;
    }

    /**
     * @return Carbon|null $autoForge
     */
    public function getAutoForgeAttribute(): ?Carbon
    {
        return $this->last_auto_forge_at;
    }
}
