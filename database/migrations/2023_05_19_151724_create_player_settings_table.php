<?php

use Database\Traits\TableComment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use TableComment;

    protected string $table = 'player_settings';
    protected string $comment = '玩家自動程序設定';

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->string('player_name', 30)->primary()->comment('玩家暱稱');
            $table->jsonb('setting')->default('{}')->comment('設定');
            $table->timestampsTz(6);
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
