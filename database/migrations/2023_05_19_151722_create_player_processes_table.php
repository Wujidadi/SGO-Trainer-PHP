<?php

use Database\Traits\TableComment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use TableComment;

    protected string $table = 'player_processes';
    protected string $comment = '玩家自動程序開關';

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->string('player_name', 30)->primary()->comment('玩家暱稱');
            $table->boolean('auto_hunt')->comment('自動狩獵');
            $table->boolean('auto_mine')->comment('自動挖礦');
            $table->boolean('auto_forge')->comment('自動鍛造');
            $table->timestampsTz(6);
        });
        $this->comment();
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
