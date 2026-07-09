<?php

namespace Database\Seeders;

use App\Models\Crm\Permission;
use App\Models\Crm\Role;
use App\Models\Crm\CommissionRule;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CrmRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            ['name' => 'admin', 'display_name' => 'Quản trị viên'],
            ['name' => 'staff', 'display_name' => 'Nhân sự nội bộ'],
            ['name' => 'collaborator', 'display_name' => 'Cộng tác viên'],
            ['name' => 'packaging', 'display_name' => 'Đóng gói vận chuyển'],
            ['name' => 'accounting', 'display_name' => 'Kế toán'],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                ['display_name' => $roleData['display_name'], 'guard_name' => 'web']
            );
        }

        $permissions = [
            'crm.affiliates.manage',
            'crm.customers.manage',
            'crm.orders.manage',
            'crm.payments.manage',
            'crm.deliveries.manage',
            'crm.commissions.manage',
            'crm.reports.view',
        ];

        foreach ($permissions as $permissionName) {
            Permission::updateOrCreate(
                ['name' => $permissionName],
                ['display_name' => $permissionName]
            );
        }

        $adminRole = Role::where('name', 'admin')->first();
        $staffRole = Role::where('name', 'staff')->first();
        $collaboratorRole = Role::where('name', 'collaborator')->first();
        $packagingRole = Role::where('name', 'packaging')->first();
        $accountingRole = Role::where('name', 'accounting')->first();

        if ($adminRole) {
            $adminRole->permissions()->sync(Permission::query()->pluck('id'));
        }

        if ($staffRole) {
            $staffRole->permissions()->sync(
                Permission::query()
                    ->whereIn('name', [
                        'crm.customers.manage',
                        'crm.orders.manage',
                        'crm.payments.manage',
                        'crm.deliveries.manage',
                        'crm.commissions.manage',
                        'crm.reports.view',
                    ])
                    ->pluck('id')
            );
        }

        if ($collaboratorRole) {
            $collaboratorRole->permissions()->sync(
                Permission::query()
                    ->whereIn('name', [
                        'crm.customers.manage',
                        'crm.orders.manage',
                        'crm.commissions.manage',
                        'crm.reports.view',
                    ])
                    ->pluck('id')
            );
        }

        if ($packagingRole) {
            $packagingRole->permissions()->sync(
                Permission::query()
                    ->whereIn('name', [
                        'crm.orders.manage',
                        'crm.deliveries.manage',
                    ])
                    ->pluck('id')
            );
        }

        if ($accountingRole) {
            $accountingRole->permissions()->sync(
                Permission::query()
                    ->whereIn('name', [
                        'crm.orders.manage',
                        'crm.payments.manage',
                        'crm.reports.view',
                    ])
                    ->pluck('id')
            );
        }

        $rules = [
            [
                'name' => 'Mặc định',
                'product_category' => null,
                'rate_percent' => 5.00,
                'priority' => 100,
                'is_active' => true,
            ],
            [
                'name' => 'Xe thể thao premium',
                'product_category' => 'sports',
                'rate_percent' => 7.50,
                'priority' => 20,
                'is_active' => true,
            ],
            [
                'name' => 'CTV doanh số cao',
                'product_category' => null,
                'min_affiliate_sales_jpy' => 1000000,
                'rate_percent' => 8.00,
                'priority' => 10,
                'is_active' => true,
            ],
        ];

        foreach ($rules as $rule) {
            CommissionRule::updateOrCreate(
                ['name' => $rule['name']],
                $rule
            );
        }
    }
}
