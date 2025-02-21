<!DOCTYPE html>
<html>
<head>
    <title>Xác nhận đơn hàng</title>
</head>
<body>
    <h2>Xin chào, {{ $order->shop->user->name }}!</h2>
    <p>Cảm ơn bạn đã đặt hàng tại cửa hàng của chúng tôi.</p>
    <p><strong>Mã đơn hàng:</strong> {{ $order->order_code }}</p>
    <p><strong>Danh sách sản phẩm:</strong></p>
    <ul>      
            <li>{{ $order->total_products }}</li>
    </ul>
    <p><strong>Tổng tiền:</strong> {{ $order->total_bill }} VNĐ</p>
    <p>Bạn có đơn hàng mới!</p>
</body>
</html>
