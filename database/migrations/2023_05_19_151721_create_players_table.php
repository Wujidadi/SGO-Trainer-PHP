<?php

use Database\Traits\TableComment;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    use TableComment;

    protected string $table = 'players';
    protected string $comment = '玩家基本資料';

    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->string('name', 30)->primary()->comment('玩家暱稱');
            $table->string('token', 200)->unique()->comment('Token');
            $table->integer('order', unsigned: true)->comment('排序');
            $table->timestampsTz(6);
            $table->unique(['name', 'token']);
        });
        $this->comment();
    }

    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
