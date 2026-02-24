<?php

namespace Database\Seeders;

use App\Models\Dashboard\Admin;
use App\Models\Dashboard\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permessions = array_keys(config('permissions'));
        $role = Role::create([
            'name' => 'Admin',
            'permissions'=>$permessions
        ]);
        Admin::create([
            'name' => 'Admin',
            'email' => 'admin@example',
            'password' => bcrypt('password'),
            'role_id' => $role->id
        ]);
    }
}
