<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{

    public function run()
    {
        $roles = ['Người dùng', 'Admin', 'Nhân viên', 'Quản lý'];

        foreach ($roles as $role) {
            Role::create(['name' => $role]);
        }
    }
}
