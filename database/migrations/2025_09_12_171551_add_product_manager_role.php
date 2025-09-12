<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Thêm role Product Manager vào database
        Role::updateOrCreate(
            ['slug' => 'product_manager'],
            [
                'name' => 'Product Manager',
                'slug' => 'product_manager',
                'description' => 'Quản lý sản phẩm có quyền như seller + tạo & đăng sản phẩm như admin',
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Xóa role Product Manager
        Role::where('slug', 'product_manager')->delete();
    }
};