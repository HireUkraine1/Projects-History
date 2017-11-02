<?php

namespace App\Support\Database;

use DB;

use \Illuminate\Database\Schema\Grammars\PostgresGrammar;

/**
 * Base Migration class with handy methods.
 */
abstract class Migration extends \Illuminate\Database\Migrations\Migration
{

    /**
     * @return mixed
     */
    protected function schema()
    {
        return $this->get_schema();
    }

    /**
     * @return mixed
     */
    protected function get_schema()
    {
        DB::setSchemaGrammar(new PostgresGrammar());

        $schema = DB::getSchemaBuilder();
        $schema->blueprintResolver(function ($table, $callback) {
            return new Blueprint($table, $callback);
        });

        return $schema;
    }
}
