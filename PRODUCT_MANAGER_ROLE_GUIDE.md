# Hướng dẫn sử dụng Role Product Manager

## 📋 Tổng quan

Role **Product Manager** là một role mới được tạo để có quyền như **Seller** + thêm quyền **Tạo & Đăng sản phẩm** như **Admin**.

## 🎯 Quyền hạn

### ✅ Quyền giống Seller:
- **Quản lý đơn hàng** - Xem và xử lý đơn hàng
- **Tài chính** - Nạp tiền, Lịch sử giao dịch, Biến động số dư, Báo cáo quyết toán
- **Dịch Vụ DropShip** - Quảng cáo, Gói đăng sản phẩm, Tính % chiến dịch
- Quản lý cửa hàng của mình
- Xem báo cáo và thống kê

### ✅ Quyền thêm như Admin:
- **Tạo & Đăng sản phẩm** - Menu riêng biệt
  - **Tạo gói** (trang /program)
  - **Danh sách gói** (quản lý gói đăng sản phẩm)
- **Tự động hóa gói đăng sản phẩm**

## 🔧 Cách sử dụng

### 1. Gán role cho user:

```php
// Gán role Product Manager cho user
$user = User::find($userId);
$user->assignRole('product_manager');
```

### 2. Kiểm tra quyền truy cập:

```php
// Kiểm tra user có role Product Manager không
if ($user->hasRole('product_manager')) {
    // User có quyền Product Manager
}

// Kiểm tra user có quyền truy cập trang admin không
if ($user->hasRole(['admin', 'product_manager'])) {
    // User có quyền truy cập
}
```

### 3. Sử dụng trong middleware:

```php
// Trong routes/web.php
Route::middleware(['auth', 'role:admin,product_manager'])->group(function () {
    Route::get('/program', [ProgramController::class, 'program']);
    Route::post('/program', [ProgramController::class, 'store']);
    // Các route khác cho gói đăng sản phẩm
});
```

### 4. Sử dụng trong controller:

```php
// Trong controller
public function someMethod()
{
    if (auth()->user()->hasRole(['admin', 'product_manager'])) {
        // Logic cho admin và product manager
    } elseif (auth()->user()->hasRole('seller')) {
        // Logic cho seller
    }
}
```

## 📊 So sánh các role

| Quyền | Admin | Product Manager | Seller |
|-------|-------|-----------------|--------|
| Quản lý hệ thống | ✅ | ❌ | ❌ |
| Tạo gói đăng sản phẩm | ✅ | ✅ | ❌ |
| Quản lý cửa hàng | ✅ | ✅ | ✅ |
| Xem đơn hàng | ✅ | ✅ | ✅ |
| Xử lý đơn hàng | ✅ | ✅ | ✅ |

## 🚀 Tính năng mới

### Tự động hóa gói đăng sản phẩm:
1. **Tạo gói** → Tự động đăng ký cho shop được chọn
2. **Tự động thực hiện** → Chuyển trạng thái "Đang triển khai"
3. **Tự động hoàn thành** → Chuyển trạng thái "Đã triển khai"
4. **Tự động thu phí** → Trừ tiền shop owner

### Mã thanh toán thống nhất:
- Format: `FT` + 14 chữ số ngẫu nhiên
- Thống nhất giữa lịch sử giao dịch và biến động số dư

## 🔐 Bảo mật

- Role Product Manager **KHÔNG** có quyền quản lý hệ thống như Admin
- Chỉ có quyền tạo và quản lý gói đăng sản phẩm
- Vẫn bị giới hạn bởi các middleware bảo mật khác

## 📝 Lưu ý

1. **Role ID**: 7
2. **Slug**: `product_manager`
3. **Mô tả**: "Quản lý sản phẩm có quyền như seller + tạo & đăng sản phẩm như admin"
4. **Tương thích**: Hoạt động với middleware `CheckRole` hiện có
5. **Mở rộng**: Có thể thêm quyền khác nếu cần

## 🎉 Kết luận

Role Product Manager đã được tạo thành công và sẵn sàng sử dụng. User với role này sẽ có quyền như Seller + thêm quyền tạo và quản lý gói đăng sản phẩm như Admin.
