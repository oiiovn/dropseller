<?php
require __DIR__.'/../vendor/autoload.php';

use danog\MadelineProto\API;
use danog\MadelineProto\Settings;
use danog\MadelineProto\Settings\AppInfo;

try {
    // Chuẩn bị settings mới cho MadelineProto v8
    $settings = (new Settings)
        ->setAppInfo(
            (new AppInfo)
                ->setApiId(29235192)
                ->setApiHash('2cc9fd5cbac4bd082b61f1361642a0a7')
        );

    // Khởi tạo MadelineProto chuẩn
    $MadelineProto = new API('session.madeline', $settings);

    // Nhận dữ liệu JSON
    $data = json_decode(file_get_contents('php://input'), true);

    if (!isset($data['message'])) {
        http_response_code(400);
        echo json_encode(['status' => 'error', 'error' => 'Missing message']);
        exit;
    }

    // Gửi tin nhắn đến bot @le_van_manh_bot
    $response = $MadelineProto->messages->sendMessage([
        'peer' => '@le_van_manh_bot',
        'message' => $data['message'],
    ]);

    // Trả kết quả thành công
    echo json_encode([
        'status' => 'success',
        'response' => $response
    ]);
} catch (\Throwable $e) {
    // Nếu có lỗi, trả về JSON lỗi
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'error' => $e->getMessage()
    ]);
}
?>
