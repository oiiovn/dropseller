<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create([
            'name' => 'Administrator',
            'slug' => 'admin',
            'description' => 'Quản trị viên hệ thống'
        ]);

        Role::create([
            'name' => 'Seller',
            'slug' => 'seller',
            'description' => 'Người bán hàng'
        ]);
    }
}
