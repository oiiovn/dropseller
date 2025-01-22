@extends('layout')
@section('title', 'Quản lý shop')

@section('main')
<div class="container-full rounded-1 bg-white">
    <div class="ps-3 pt-3">
        <h4 >Quản lý shop</h4>
    </div>
    <hr style="border: none; border-top: 1px dashed rgba(0, 0, 0, 0.5);">
    <form class="d-flex" action="{{ route('shops.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row col-12 mx-auto">
            <div class="col-6 d-flex align-items-center">
                <label class=" align-items-center col-3" for="file" style="margin-bottom:0;">Chọn file Excel:</label>
                <input type="file" class="form-control" name="file" id="file" accept=".xlsx,.xls,.csv" required>
            </div>
            <div class="col-3 d-flex align-items-center gap-3">
                <button type="submit" class="btn btn-secondary bg-gradient waves-effect waves-light">Nhập Dữ Liệu</button>
                <a class="btn btn-danger bg-gradient waves-effect waves-light" data-bs-toggle="modal" href="#modalthemshop" role="button">Thêm Shop</a>
            </div>
        </div>
    </form>
    <table class="table table-hover table-striped table-bordered mt-2">
        <div class="modal fade" id="modalthemshop" aria-hidden="true" aria-labelledby="modalthemshopLabel" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalthemshopLabel">Nhập thông tin shop </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="container">
                            <form action="{{ route('shops.store') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <input type="text" name="shop_id" id="shop_id" class="form-control" placeholder="ID Shop" required>
                                    
                                </div>
                                <div class="mb-3">
                                    <input type="text" name="shop_name" id="shop_name" class="form-control" placeholder="Tên Shop" required>
                                </div>
                                <div class="mb-3">
                                    <select name="user_id" id="user_id" class="form-control" placeholder="Người dùng" required>
                                        <option value="" disabled selected>Chọn chủ shop</option>
                                        @foreach($users as $user)
                                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="btn btn-danger bg-gradient waves-effect waves-light">Thêm Shop</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <thead>
            <tr class="bg-primary-subtle">
                <th>ID</th>
                <th>Tên Shop</th>
                <th>Người dùng</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shops as $shop)
            <tr>
                <td>{{ $shop->shop_id }}</td>
                <td>{{ $shop->shop_name }}</td>
                <td>{{ $shop->user->name ?? 'Chủ shop' }}</td>
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
                                                <option value="" disabled {{ old('user_id', $shop->user_id) ? '' : 'selected' }}>Chọn chủ shop</option>
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
                    <a class="btn btn-primary btn-sm" data-bs-toggle="modal" href="#editModal-{{ $shop->id }}" role="button">Sửa</a>
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