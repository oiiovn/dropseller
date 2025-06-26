<?php

return [
    'default' => [
        'length' => 6, // Độ dài của mã là 6 ký tự
        'width' => 160,
        'height' => 46,
        'quality' => 90,
        'math' => false, // Không sử dụng toán học
        'expire' => 60,
        'encrypt' => false,
        'bgColor' => '#ffffff',
        'fontColors' => ['#1f75fe', '#9c27b0', '#e91e63', '#f44336', '#4caf50', '#009688'],
        'characters' => '123456789ABCDEFGHJKLMNPQRSTUVWXYZ', // Không dùng chữ/số dễ nhầm lẫn
    ],
];
