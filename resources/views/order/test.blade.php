@extends('layout')
@section('title', 'main')
@section('main')
<form action="{{ url('/import-don-hoan') }}" method="POST" enctype="multipart/form-data">
    @csrf
    <input type="file" name="file" required>
    <button type="submit">Tải lên và xử lý</button>
</form>

@if (isset($ketQua))
<h4>Kết quả đối soát:</h4>
<table cellpadding="6" style="border-collapse: collapse">
    <thead>
        <tr>
            <th>Ngày</th>
            <th>Shop ID</th>
            <th>Mã đơn</th>
            <th>Ngày lọc</th>
            <th>SKU</th>
            <th>Ghi chú</th>
        </tr>
    </thead>
    <tbody>
        @foreach($ketQua as $item)
        <tr>
            <td>{{ $item['ngay'] }}</td>
            <td>{{ $item['shop_id'] }}</td>
            <td>{{ $item['order_code'] }}</td>
            <td>{{ $item['filter_date'] }}</td>
            <td>{{ $item['sku'] }}</td>
            <td>{{ $item['ket_qua'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif
@endsection