<?php

namespace App\Support\Database;

class Blueprint extends \Illuminate\Database\Schema\Blueprint
{

    /**
     * Base model colums
     */
    public function model_columns()
    {
        $this->increments('id');
        $this->timestamps();
        $this->softDeletes();
    }

    /**
     * @param string $attr
     * @param string|void $table
     */
    public function foreign_id($attr, $table = null)
    {
        $col = $attr.'_id';
        $table = $table ?: str_plural($attr);

        $col_print = $this->integer($col)->unsigned();
        $this->foreign($col)->references('id')->on($table);

        return $col_print;
    }

    /**
     * @param string $column
     */
    public function dropForeignColumn($column)
    {
        $this->dropForeign($this->table.'_'.$column.'_foreign');
        $this->dropColumn($column);
    }

    /**
     * @param string $column
     */
    public function dropIndexColumn($column)
    {
        $this->dropIndex($this->table.'_'.$column.'_index');
        $this->dropColumn($column);
    }
}