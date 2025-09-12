<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Models\User;
use App\Models\Role;

echo "=== KIỂM TRA QUYỀN TRUY CẬP ROLE PRODUCT MANAGER ===\n";

// 1. Tìm user có role Product Manager
$productManagerUser = User::whereHas('roles', function($query) {
    $query->where('slug', 'product_manager');
})->first();

if (!$productManagerUser) {
    echo "❌ Không tìm thấy user có role Product Manager\n";
    exit;
}

echo "✅ User: " . $productManagerUser->name . " (ID: " . $productManagerUser->id . ")\n\n";

// 2. Kiểm tra các role
$roles = $productManagerUser->roles;
echo "📋 Roles của user:\n";
foreach ($roles as $role) {
    echo "   - " . $role->name . " (" . $role->slug . ")\n";
}
echo "\n";

// 3. Kiểm tra quyền truy cập các nhóm middleware
echo "🔍 KIỂM TRA QUYỀN TRUY CẬP:\n\n";

// 3.1. Middleware 'checkrole' (không có role cụ thể)
echo "1. Middleware 'checkrole' (không có role cụ thể):\n";
echo "   ✅ Có quyền truy cập (tất cả user đã đăng nhập)\n";
echo "   📄 Các trang: Thu chi, Công cụ, Đơn hàng, Cập nhật profile, Thông báo, Báo cáo quyết toán\n\n";

// 3.2. Middleware 'checkrole:seller,product_manager'
echo "2. Middleware 'checkrole:seller,product_manager':\n";
$hasSellerRole = $productManagerUser->hasRole('seller');
$hasProductManagerRole = $productManagerUser->hasRole('product_manager');
$hasAccessToSellerRoutes = $productManagerUser->hasRole(['seller', 'product_manager']);
echo "   - Có role Seller: " . ($hasSellerRole ? 'CÓ' : 'KHÔNG') . "\n";
echo "   - Có role Product Manager: " . ($hasProductManagerRole ? 'CÓ' : 'KHÔNG') . "\n";
echo "   - Có quyền truy cập: " . ($hasAccessToSellerRoutes ? 'CÓ' : 'KHÔNG') . "\n";
if ($hasAccessToSellerRoutes) {
    echo "   ✅ Có quyền truy cập\n";
    echo "   📄 Các trang: Quản lý đơn hàng, Đăng ký gói shop, Quyết toán, Quảng cáo shop, Lịch sử số dư, Gói đăng sản phẩm\n";
} else {
    echo "   ❌ KHÔNG có quyền truy cập\n";
    echo "   📄 Các trang bị hạn chế: Quản lý đơn hàng, Đăng ký gói shop, Quyết toán, Quảng cáo shop, Lịch sử số dư, Gói đăng sản phẩm\n";
}
echo "\n";

// 3.3. Middleware 'checkrole:admin,manager,product_manager'
echo "3. Middleware 'checkrole:admin,manager,product_manager':\n";
$hasAdminRole = $productManagerUser->hasRole('admin');
$hasManagerRole = $productManagerUser->hasRole('manager');
$hasProductManagerRole2 = $productManagerUser->hasRole('product_manager');
$hasAccessToAdminRoutes = $productManagerUser->hasRole(['admin', 'manager', 'product_manager']);

echo "   - Có role Admin: " . ($hasAdminRole ? 'CÓ' : 'KHÔNG') . "\n";
echo "   - Có role Manager: " . ($hasManagerRole ? 'CÓ' : 'KHÔNG') . "\n";
echo "   - Có role Product Manager: " . ($hasProductManagerRole2 ? 'CÓ' : 'KHÔNG') . "\n";
echo "   - Có quyền truy cập: " . ($hasAccessToAdminRoutes ? 'CÓ' : 'KHÔNG') . "\n";

if ($hasAccessToAdminRoutes) {
    echo "   ✅ Có quyền truy cập\n";
    echo "   📄 Các trang: Tất cả đơn hàng, Quản lý shop, Sản phẩm, Tạo gói, Danh sách gói, Quảng cáo, Nạp tiền khách hàng, Phí web, Lỗi số dư AI, Công cụ, Hoàn đơn, Quyết toán drop\n";
} else {
    echo "   ❌ KHÔNG có quyền truy cập\n";
}
echo "\n";

// 3.4. Middleware 'role:admin'
echo "4. Middleware 'role:admin' (chỉ Admin):\n";
if ($hasAdminRole) {
    echo "   ✅ Có quyền truy cập\n";
    echo "   📄 Các trang: Dashboard admin, Quản lý người dùng, Shop, Giao dịch, Sản phẩm, Báo cáo, Thông báo\n";
} else {
    echo "   ❌ KHÔNG có quyền truy cập (chỉ dành cho Admin)\n";
    echo "   📄 Các trang bị hạn chế: Dashboard admin, Quản lý người dùng, Shop, Giao dịch, Sản phẩm, Báo cáo, Thông báo\n";
}
echo "\n";

// 4. Tóm tắt quyền truy cập
echo "📊 TÓM TẮT QUYỀN TRUY CẬP:\n";
echo "✅ CÓ QUYỀN TRUY CẬP:\n";
echo "   - Tất cả trang cơ bản (checkrole)\n";
if ($hasAccessToSellerRoutes) {
    echo "   - Tất cả trang Seller/Product Manager\n";
}
if ($hasAccessToAdminRoutes) {
    echo "   - Tất cả trang Admin/Manager/Product Manager\n";
}

echo "\n❌ KHÔNG CÓ QUYỀN TRUY CẬP:\n";
if (!$hasAccessToSellerRoutes) {
    echo "   - Các trang Seller (cần thêm quyền)\n";
}
if (!$hasAdminRole) {
    echo "   - Các trang Admin thuần túy (đúng như thiết kế)\n";
}

// 5. Khuyến nghị
echo "\n💡 KHUYẾN NGHỊ:\n";
if (!$hasAccessToSellerRoutes) {
    echo "⚠️  Product Manager cần có quyền Seller để truy cập đầy đủ\n";
    echo "   - Cần cập nhật middleware 'checkrole:seller' thành 'checkrole:seller,product_manager'\n";
    echo "   - Hoặc gán thêm role Seller cho user Product Manager\n";
} else {
    echo "✅ Product Manager đã có quyền truy cập đầy đủ!\n";
}

echo "\n=== HOÀN THÀNH KIỂM TRA ===\n";
