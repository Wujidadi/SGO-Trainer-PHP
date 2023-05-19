<?php

namespace Database\Traits;

use DB;

trait TableComment
{
    /**
     * Add comment to table. Only support MySQL and PostgreSQL.
     *
     * @return void
     */
    public function comment(): void
    {
        switch (config('database.default')) {
            case 'mysql':
                DB::statement("ALTER TABLE $this->table comment '$this->comment'");
                break;
            case 'pgsql':
                DB::statement("COMMENT ON TABLE $this->table IS '$this->comment'");
                break;
            default:
                break;
        }
    }
}
