<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait HasCompoundIndex
{
    private function tableIndexExists(string $index): bool
    {
        $table = $this->getTable();
        $index = strtolower($index);
        $indices = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableIndexes($table);

        return array_key_exists($index, $indices);
    }

    public function scopeUseIndex($query, string $index)
    {
        $table = $this->getTable();

        return $this->tableIndexExists($index)
            ? $query->from(DB::raw("`$table` USE INDEX(`$index`)"))
            : $query;
    }
}
