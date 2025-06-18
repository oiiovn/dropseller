<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the core roles
        $roles = [
            [
                'name' => 'Administrator',
                'slug' => 'admin',
                'description' => 'Quản trị viên có toàn quyền truy cập hệ thống',
            ],
            [
                'name' => 'Manager',
                'slug' => 'manager',
                'description' => 'Quản lý có quyền truy cập vào hầu hết các tính năng ngoại trừ cài đặt hệ thống',
            ],
            [
                'name' => 'Seller',
                'slug' => 'seller',
                'description' => 'Người bán có quyền quản lý các cửa hàng và sản phẩm của họ',
            ],
            [
                'name' => 'Customer',
                'slug' => 'customer',
                'description' => 'Người dùng có quyền truy cập cơ bản',
            ],
            [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Nhân viên có quyền xem và xử lý đơn hàng',
            ],
        ];

        // Insert or update roles
        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['slug' => $role['slug']],
                $role
            );
        }

        // Output success message
        $this->command->info('Roles seeded successfully');
    }
}
