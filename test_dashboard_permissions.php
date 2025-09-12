<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

echo "=== KIỂM TRA DASHBOARD PERMISSIONS ===\n";

// 1. Tìm user có role Product Manager
$productManagerUser = User::whereHas('roles', function($query) {
    $query->where('slug', 'product_manager');
})->first();

if (!$productManagerUser) {
    echo "❌ Không tìm thấy user có role Product Manager\n";
    exit;
}

echo "✅ User: " . $productManagerUser->name . " (ID: " . $productManagerUser->id . ")\n\n";

// 2. Kiểm tra logic phân quyền dashboard
echo "🔍 KIỂM TRA LOGIC PHÂN QUYỀN DASHBOARD:\n\n";

// Lấy role slug
$roleSlug = DB::table('role_user')
    ->join('roles', 'role_user.role_id', '=', 'roles.id')
    ->where('role_user.user_id', $productManagerUser->id)
    ->value('roles.slug');

echo "Role slug: " . $roleSlug . "\n";

// Kiểm tra logic cũ
$isAdminOrManager_old = in_array($roleSlug, ['admin', 'manager']);
$isSeller_old = $roleSlug === 'seller';

echo "\n📊 LOGIC CŨ:\n";
echo "- isAdminOrManager: " . ($isAdminOrManager_old ? 'TRUE' : 'FALSE') . "\n";
echo "- isSeller: " . ($isSeller_old ? 'TRUE' : 'FALSE') . "\n";

// Kiểm tra logic mới
$isAdminOrManager_new = in_array($roleSlug, ['admin', 'manager']);
$isSeller_new = in_array($roleSlug, ['seller', 'product_manager']);

echo "\n📊 LOGIC MỚI:\n";
echo "- isAdminOrManager: " . ($isAdminOrManager_new ? 'TRUE' : 'FALSE') . "\n";
echo "- isSeller: " . ($isSeller_new ? 'TRUE' : 'FALSE') . "\n";

// Xác định loại dashboard
echo "\n🎯 KẾT QUẢ DASHBOARD:\n";
if ($isAdminOrManager_new) {
    echo "✅ Hiển thị dashboard Admin/Manager (tất cả dữ liệu hệ thống)\n";
} elseif ($isSeller_new) {
    echo "✅ Hiển thị dashboard Seller/Product Manager (dữ liệu theo shop của user)\n";
} else {
    echo "❌ Không có quyền truy cập dashboard\n";
}

// 3. So sánh với user Seller
echo "\n🔄 SO SÁNH VỚI USER SELLER:\n";

$sellerUser = User::whereHas('roles', function($query) {
    $query->where('slug', 'seller');
})->first();

if ($sellerUser) {
    $sellerRoleSlug = DB::table('role_user')
        ->join('roles', 'role_user.role_id', '=', 'roles.id')
        ->where('role_user.user_id', $sellerUser->id)
        ->value('roles.slug');
    
    echo "Seller User: " . $sellerUser->name . " (Role: " . $sellerRoleSlug . ")\n";
    
    $sellerIsAdminOrManager = in_array($sellerRoleSlug, ['admin', 'manager']);
    $sellerIsSeller = in_array($sellerRoleSlug, ['seller', 'product_manager']);
    
    echo "- isAdminOrManager: " . ($sellerIsAdminOrManager ? 'TRUE' : 'FALSE') . "\n";
    echo "- isSeller: " . ($sellerIsSeller ? 'TRUE' : 'FALSE') . "\n";
    
    if ($sellerIsAdminOrManager) {
        echo "✅ Seller hiển thị dashboard Admin/Manager\n";
    } elseif ($sellerIsSeller) {
        echo "✅ Seller hiển thị dashboard Seller\n";
    }
    
    // So sánh
    if ($isSeller_new && $sellerIsSeller) {
        echo "\n🎉 Product Manager và Seller có cùng loại dashboard!\n";
    } else {
        echo "\n⚠️  Product Manager và Seller có dashboard khác nhau\n";
    }
}

echo "\n=== HOÀN THÀNH KIỂM TRA ===\n";
