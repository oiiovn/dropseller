@extends('layout')
@section('title', 'main')
@section('main')
<div class="container mt-4">
    <h1 class="text-center">Báo cáo sản phẩm</h1>

    @if (!empty($filteredProducts))
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Mã sản phẩm</th>
                    <th>Tên sản phẩm</th>
                    <th>Số lượng bán</th>
                    <th>Doanh thu</th>
                    <th>Hình ảnh</th>
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
