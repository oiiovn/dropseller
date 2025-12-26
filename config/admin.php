<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Access Code
    |--------------------------------------------------------------------------
    |
    | Đây là mã cố định dùng để truy cập các chức năng quản trị cao cấp.
    | Không nên thay đổi mã này trừ khi thực sự cần thiết.
    |
    */
    'access_code' => env('ADMIN_ACCESS_CODE', 'admin@1994'),
    
    /*
    |--------------------------------------------------------------------------
    | Admin Access Expiration
    |--------------------------------------------------------------------------
    |
    | Thời gian hiệu lực của xác thực mã truy cập (tính bằng phút).
    | Sau khoảng thời gian này, admin sẽ cần nhập lại mã.
    |
    */
    'access_expiration' => env('ADMIN_ACCESS_EXPIRATION', 60), // 60 phút = 1 giờ
];
