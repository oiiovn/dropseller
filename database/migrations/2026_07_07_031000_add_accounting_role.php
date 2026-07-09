<?php

use App\Models\Crm\Permission;
use App\Models\Crm\Role;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $role = Role::updateOrCreate(
            ['name' => 'accounting'],
            ['display_name' => 'Kế toán', 'guard_name' => 'web']
        );

        $permissionIds = Permission::query()
            ->whereIn('name', [
                'crm.orders.manage',
                'crm.payments.manage',
                'crm.reports.view',
            ])
            ->pluck('id');

        if ($permissionIds->isNotEmpty()) {
            $role->permissions()->syncWithoutDetaching($permissionIds);
        }
    }

    public function down(): void
    {
        $role = Role::where('name', 'accounting')->first();
        if ($role) {
            $role->permissions()->detach();
            $role->delete();
        }
    }
};
