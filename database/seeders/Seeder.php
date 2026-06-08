<?php
namespace Database\Seeders;

use DB;
use Illuminate\Database\Seeder as LaravelSeeder;

abstract class Seeder extends LaravelSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    abstract public function run();


    /**
     * Truncate tables.
     *
     * @param array $tables
     * @return void
     */
    protected function truncate($tables)
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            DB::table($table)->truncate();
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}

