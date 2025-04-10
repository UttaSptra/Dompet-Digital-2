<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRoleId = DB::table('roles')->where('name', 'Admin')->value('id');
        $bankRoleId = DB::table('roles')->where('name', 'Bank')->value('id');
        $siswaRoleId = DB::table('roles')->where('name', 'Siswa')->value('id');

        User::create([
            'name' => 'Admin',
            'role_id' => $adminRoleId,
            'email' => 'admin@example.com',
            'password' => bcrypt('123'),
            
        ]);

        User::create([
            'name' => 'Bank',
            'role_id' => $bankRoleId,
            'email' => 'bank@example.com',
            'password' => bcrypt('123'),
        ]);

        User::create([
            'name' => 'Siswa',
            'role_id' => $siswaRoleId,
            'email' => 'siswa@example.com',
            'password' => bcrypt('123'),
        ]);
    }
}
