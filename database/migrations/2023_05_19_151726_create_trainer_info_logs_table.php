<?php

use Database\Traits\TableComment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use TableComment;

    protected string $table = 'trainer_info_logs';
    protected string $comment = '自動程序非錯誤日誌';

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->id();
            $table->timestampTz('time', 6)->index()->comment('時間');
            $table->string('player_name', 30)->index()->comment('玩家暱稱');
            $table->string('category', 30)->index()->comment('分類');
            $table->text('message')->comment('日誌訊息');
        });
        $this->comment();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
