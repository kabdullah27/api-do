<?php

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => DB::raw('UUID()'),
            'user_code' => 'abdul',
            'password' => bcrypt('password'),
        ]);
    }
}
