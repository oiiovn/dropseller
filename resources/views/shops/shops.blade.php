@extends('layout')
@section('title', 'main')
@section('main')
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nhập Dữ Liệu Shop</title>
</head>
<body>
    <h1>Nhập Dữ Liệu Shop từ File Excel</h1>

    <!-- Hiển thị thông báo -->
    @if (session('success'))
        <div style="color: green;">
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div style="color: red;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Form upload file Excel -->
    <form action="{{ route('shops.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="file">Chọn file Excel:</label>
        <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required>
        <button type="submit">Nhập Dữ Liệu</button>
    </form>
    





</body>
</html>
@endsection