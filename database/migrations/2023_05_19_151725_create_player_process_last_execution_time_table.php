<?php

use Database\Traits\TableComment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use TableComment;

    protected string $table = 'player_process_last_action_time';
    protected string $comment = '玩家自動程序最後動作時間';

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->string('player_name', 30)->primary()->comment('玩家暱稱');
            $table->timestampTz('created_at', 6)->nullable();
            $table->timestampTz('last_auto_hunt_at', 6)->nullable()->comment('自動狩獵最後動作時間');
            $table->timestampTz('last_auto_mine_at', 6)->nullable()->comment('自動挖礦最後動作時間');
            $table->timestampTz('last_auto_forge_at', 6)->nullable()->comment('自動鍛造最後動作時間');
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
