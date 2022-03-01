<?php

namespace Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        ini_set('memory_limit','512M');

        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        DB::table('roles')->truncate();
        DB::table('users')->truncate();
        DB::table('profiles')->truncate();
        DB::table('loans')->truncate();
        DB::table('payments')->truncate();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
    }
}
