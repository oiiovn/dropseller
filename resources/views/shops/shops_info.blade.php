@extends('layout')
@section('title', 'Quản lý shop')

@section('main')
<style>
    .shop-card {
        border-radius: 16px;
        box-shadow: 0 2px 12px 0 rgba(0,0,0,0.07);
        transition: box-shadow 0.2s, transform 0.2s;
        border: none;
        background: #fff;
    }
    .shop-card:hover {
        box-shadow: 0 6px 24px 0 rgba(0,0,0,0.13);
        transform: translateY(-4px) scale(1.02);
    }
    .shop-logo {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        object-fit: contain;
        margin-right: 8px;
       
    }
    .shop-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #222;
        margin-bottom: 2px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 180px;
        display: block;
    }
    .shop-owner {
        color: #4b5563;
        font-size: 0.95rem;
        margin-bottom: 2px;
    }
    .shop-id {
        color: #4b5563;
        font-size: 0.73rem;
        font-weight: 400;
        margin-bottom: 0;
    }
    .shop-actions {
        margin-top: 10px;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }
    .shop-actions .btn-primary {
        background: #3b4781;
        border-radius: 8px;
        font-weight: 500;
        padding: 4px 18px;
        border: none;
    }
    .shop-actions .btn-primary:hover {
        background: #232a4d;
    }
    .shop-actions .btn-danger {
        background: #f46a3c;
        border-radius: 8px;
        font-weight: 500;
        padding: 4px 18px;
        border: none;
    }
    .shop-actions .btn-danger:hover {
        background: #c13c0b;
    }
    @media (max-width: 768px) {
        .shop-logo { width: 44px; height: 44px; margin-right: 10px; }
        .shop-title { font-size: 1rem; }
    }
</style>
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
                                <select name="platform" id="platform" class="form-control" required>
                                    <option value="" disabled selected>Chọn Platform</option>
                                    <option value="Shoppe">Shoppe</option>
                                    <option value="Tiktok">Tiktok</option>
                                </select>
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
    <!-- Danh sách shop dạng card -->
    <div class="row mt-2 mx-0">
        @foreach($shops as $shop)
        <div class="col-lg-2 col-md-6 col-sm-12 mb-3">
            <div class="card shop-card card-edit-trigger" data-bs-toggle="modal" data-bs-target="#editModal-{{ $shop->id }}" style="cursor:pointer;">
                <div class="p-2 d-flex align-items-center">
                    @php
                        $platformLogo = $shop->platform === 'Tiktok'
                            ? asset('assets/images/nentang/ic-svg-tiktok.svg')
                            : asset('assets/images/nentang/ic-svg-shopee.svg');
                    @endphp
                    <img src="{{ $platformLogo }}" alt="logo" class="shop-logo" >
                    <div>
                        <div class="shop-title">{{ $shop->shop_name }}</div>
                        <div class="shop-owner">{{ $shop->user->name ?? 'Chủ shop' }}</div>
                        <div class="shop-id">{{ $shop->shop_id }}</div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="editModal-{{ $shop->id }}" aria-hidden="true" aria-labelledby="editModalLabel-{{ $shop->id }}" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
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
                                    <label for="shop_id_{{ $shop->id }}" class="form-label">ID Shop</label>
                                    <input type="text" name="shop_id" id="shop_id_{{ $shop->id }}" class="form-control" value="{{ old('shop_id', $shop->shop_id) }}" required>
                                    @error('shop_id')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="shop_name_{{ $shop->id }}" class="form-label">Tên Shop</label>
                                    <input type="text" name="shop_name" id="shop_name_{{ $shop->id }}" class="form-control" value="{{ old('shop_name', $shop->shop_name) }}" required>
                                    @error('shop_name')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="platform_{{ $shop->id }}" class="form-label">Platform</label>
                                    <select name="platform" id="platform_{{ $shop->id }}" class="form-control" required>
                                        <option value="" disabled>Chọn Platform</option>
                                        <option value="Shoppe" {{ $shop->platform == 'Shoppe' ? 'selected' : '' }}>Shoppe</option>
                                        <option value="Tiktok" {{ $shop->platform == 'Tiktok' ? 'selected' : '' }}>Tiktok</option>
                                    </select>
                                    @error('platform')
                                    <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="user_id_{{ $shop->id }}" class="form-label">Người dùng</label>
                                    <select name="user_id" id="user_id_{{ $shop->id }}" class="form-control" required>
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
                                <div class="d-flex justify-content-between mt-3">
                                    <button type="submit" class="btn btn-warning">Cập nhật Shop</button>
                                </div>
                            </form>
                            <form action="{{ route('shops.destroy', $shop->id) }}" method="POST" onsubmit="return confirm('Bạn có chắc chắn muốn xóa không?')" style="display:inline;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Xóa</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection