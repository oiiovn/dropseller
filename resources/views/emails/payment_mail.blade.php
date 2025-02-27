<!DOCTYPE html>
<html>
<head>
    <title>Đơn hàng đã được nhanh toán</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .container {
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #ddd;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: auto;
            max-width: 80%;
            min-width: 320px;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        p {
            font-size: 16px;
            color: #555;
            margin: 10px 0;
        }
        .btn-container {
            margin-top: 20px;
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }
        .btn-info {
            background-color:rgb(91, 166, 247);
        }
        a{
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Xin chào, {{ $order->shop->user->name }}!</h2>
        <p>Đơn hàng  {{ $order->order_code }} đã được thanh toán</p>
        <p><strong>Shop:</strong> <span>{{ $order->shop->shop_name }}</span></p>
        <p><strong>Mã đơn hàng:</strong> {{ $order->order_code }}</p>
        <p><strong>Số lượng sản phẩm:</strong> {{ $order->total_products }}</p>
        <p><strong>Tổng tiền:</strong> {{ number_format($order->total_bill) }} VNĐ</p>
    </div>
</body>
</html>
