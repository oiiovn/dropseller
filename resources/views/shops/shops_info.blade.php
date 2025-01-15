@extends('layout')
@section('title', 'Danh sách Shops')

@section('main')
<div class="container">
    <h1>Danh sách Shops</h1>

    <!-- Form upload file Excel -->
    <form action="{{ route('shops.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <label for="file">Chọn file Excel:</label>
        <input type="file" name="file" id="file" accept=".xlsx,.xls,.csv" required>
        <button type="submit">Nhập Dữ Liệu</button>
    </form>

    <table class="table table-bordered">
        <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalToggleLabel">Thêm Shop</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">

                            <form action="{{ route('shops.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="shop_id" class="form-label">ID Shop</label>
                                    <input type="text" name="shop_id" id="shop_id" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="shop_name" class="form-label">Tên Shop</label>
                                    <input type="text" name="shop_name" id="shop_name" class="form-control" required>
                                </div>
                                <div class="mb-3">
                                    <label for="user_id" class="form-label">Người dùng</label>
                                    <select name="user_id" id="user_id" class="form-control" required>
                                        <option value="" disabled selected>Chọn người dùng</option>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-success">Thêm Shop</button>
                            </form>


                        </div>
                    </div>
                </div>
            </div>
        </div>
        <a class="btn btn-primary" data-bs-toggle="modal" href="#exampleModalToggle" role="button">Thêm Shop</a>
        <thead>

            <tr>
                <th>ID</th>
                <th>Tên Shop</th>
                <th>User</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shops as $shop)
            <tr>
                <td>{{ $shop->shop_id }}</td>
                <td>{{ $shop->shop_name }}</td>
                <td>{{ $shop->user->name ?? 'chưa có người dùng' }}</td>
                <td>
                    <!-- Modal -->
                    <div class="modal fade" id="editModal-{{ $shop->id }}" aria-hidden="true" aria-labelledby="editModalLabel-{{ $shop->id }}" tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="editModalLabel-{{ $shop->id }}">Sửa Shop</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{ route('shops.update', $shop->id) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="mb-3">
                                            <label for="shop_id" class="form-label">ID Shop</label>
                                            <input type="text" name="shop_id" id="shop_id" class="form-control" value="{{ old('shop_id', $shop->shop_id) }}" required>
                                            @error('shop_id')
                                            <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="shop_name" class="form-label">Tên Shop</label>
                                            <input type="text" name="shop_name" id="shop_name" class="form-control" value="{{ old('shop_name', $shop->shop_name) }}" required>
                                            @error('shop_name')
                                            <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <div class="mb-3">
                                            <label for="user_id" class="form-label">Người dùng</label>
                                            <select name="user_id" id="user_id" class="form-control" required>
                                                <option value="" disabled {{ old('user_id', $shop->user_id) ? '' : 'selected' }}>Chọn người dùng</option>
                                                @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ old('user_id', $shop->user_id) == $user->id ? 'selected' : '' }}>
                                                    {{ $user->name }}
                                                </option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                            <div class="text-danger">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        <button type="submit" class="btn btn-warning">Cập nhật Shop</button>
                                    </form>

                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- Nút Sửa -->
                    <a class="btn btn-primary" data-bs-toggle="modal" href="#editModal-{{ $shop->id }}" role="button">Sửa</a>



                    <!-- Nút Xóa -->
                    <form action="{{ route('shops.destroy', $shop->id) }}" method="POST" style="display: inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Bạn có chắc chắn muốn xóa không?')">Xóa</button>
                    </form>

                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Nút Thêm -->

</div>
@endsection