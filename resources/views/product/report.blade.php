@extends('layout')
@section('title', 'main')
@section('main')
<div class="container mt-4">
    <form action="{{ route('product.report') }}" method="post">
        @csrf
        <label for="start_date">Ngày bắt đầu:</label>
        <input type="date" name="start_date" required>

        <label for="end_date">Ngày kết thúc:</label>
        <input type="date" name="end_date" required>

        <label for="platform">Nền tảng:</label>
        <select name="platform">
            <option value="Tiktok">Tiktok</option>
            <option value="Shopee">Shopee</option>
            <option value="Lazada">Lazada</option>
        </select>

        <label for="shop_id">Shop:</label>
        <select name="shop_id" id="shop_id">
            <option value="">-- Chọn Shop --</option>
            @if (!empty($shop_get) && count($shop_get) > 0)
            @foreach($shop_get as $shop)
            <option value="{{ $shop['shop_id'] ?? $shop->shop_id }}">{{ $shop['shop_name'] ?? $shop->shop_name }}</option>
            @endforeach
            @else
            <option value="">Không có shop nào</option>
            @endif
        </select>


        <button type="submit">Lọc dữ liệu</button>
    </form>
    <h1 class="text-center">Báo cáo sản phẩm</h1>
    @if (!empty($filteredProducts) && count($filteredProducts) > 0 && !empty($filterDate))
    <form action="{{ route('order.im') }}" method="POST">
        @csrf
        <input type="hidden" name="data" value="{{ json_encode($filteredProducts) }}">
        <input type="hidden" name="filterDate" value="{{ $filterDate }}">
        <button type="submit" class="btn btn-primary">Xuất hóa đơn</button>
    </form>
    @endif


    @if (!empty($filteredProducts))
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mã sản phẩm</th>
                <th>Tên sản phẩm</th>
                <th>Số lượng bán</th>
                <th>Doanh thu</th>
                <th>Hình ảnh</th>
                <th>


                </th>
            </tr>
        </thead>
        <tbody>
            @foreach ($filteredProducts as $product)
            <tr>
                <td>{{ $product['code'] ?? 'Không rõ' }}</td>
                <td>{{ $product['name'] ?? 'Không rõ' }}</td>
                <td>{{ $product['amount'] ?? 0 }}</td>
                <td>{{ number_format($product['revenue'] ?? 0) }} VNĐ</td>
                <td>
                    @if (!empty($product['image']))
                    <img src="{{ $product['image'] }}" alt="Hình ảnh" style="width: 100px;">
                    @else
                    Không có hình ảnh
                    @endif
                </td>

            </tr>
            @endforeach
        </tbody>
    </table>
    @else
    <p>Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</p>
    @endif
</div>
@endsection