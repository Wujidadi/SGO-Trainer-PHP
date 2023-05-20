<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * 自動程序非錯誤日誌資料模型
 *
 * @property int $id 主鍵，流水號
 * @property Carbon $time 時間
 * @property string $player_name 玩家暱稱
 * @property string $category 分類
 * @property string $message 日誌訊息
 */
class TrainerInfoLogs extends Model
{
    protected $table = 'trainer_info_logs';

    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'integer';

    protected $created_at = false;
    protected $updated_at = false;

    protected $fillable = [
        'time',
        'player_name',
        'category',
        'message',
    ];

    protected $hidden = [];

    protected $casts = [
        'id' => 'integer',
        'time' => 'datetime:Y-m-d H:i:s.u',
    ];
}
