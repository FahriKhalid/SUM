<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('ms_user')->insert([
            'nama' => 'alif',
            'email' => 'setiagung0120@gmail.com',
            'username' => 'superadmin',
            'password' => bcrypt('superadmin'), 
            'id_role' => '1'
        ]);
    }
}
