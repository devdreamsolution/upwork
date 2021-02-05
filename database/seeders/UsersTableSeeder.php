<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeders.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'password' => bcrypt('pass'),
            'api_token' => Str::random(\Config::get('auth.api_token_length')),
        ]);
    }
}