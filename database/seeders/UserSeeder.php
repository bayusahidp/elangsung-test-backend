<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = User::create([
            'name' => 'admin',
            'email' => 'admin@admin.com',
            'username' => 'admin',
            'fakultas' => 'admin',
            'jurusan' => 'admin',
            'password' => Hash::make('password'),
            'role' => '1',
        ]);

        $user = User::create([
            'name' => 'user',
            'email' => 'user@user.com',
            'username' => 'user',
            'fakultas' => 'user',
            'jurusan' => 'user',
            'password' => Hash::make('password'),
            'role' => '0',
        ]);
    }
}
