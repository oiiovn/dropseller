<?php
require __DIR__.'/../vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;

// Tạo Settings chuẩn
$settings = (new Settings)
    ->setAppInfo(
        (new AppInfo)
            ->setApiId(29235192) // <-- API ID của bạn
            ->setApiHash('2cc9fd5cbac4bd082b61f1361642a0a7') // <-- API HASH của bạn
    );

// Tạo MadelineProto với Settings
$MadelineProto = new API('session.madeline', $settings);

// Bắt đầu login Telegram
$MadelineProto->start();

echo "Đăng nhập thành công!\n";
?>
