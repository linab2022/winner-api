<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->toDateTimeString();
        DB::table('users')->insert([
            'name' => 'mainAdmin',
            'password' => Hash::make('mainAdmin'),
            'is_admin'=>1,
            'created_at' => $now,
            'updated_at' => $now
        ]);
        DB::table('users')->insert([
            'name' => 'Admin',
            'password' => Hash::make('Admin'),
            'is_admin'=>1,
            'created_at' => $now,
            'updated_at' => $now
        ]);
    }
}
