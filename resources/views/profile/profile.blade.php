@extends('layout')
@section('title', 'main')
@section('main')
<style>
    .tooltip-inner {
        background-color: #ffffff !important;
        color: #000 !important;
        padding: 10px 12px !important;
        border-radius: 8px;
        border: 1px solid #ddd;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        text-align: left;
        max-width: 260px;
        font-size: 13px;
        opacity: 1 !important;
    }

    .tooltip.show {
        opacity: 1 !important;
    }

    .tooltip.bs-tooltip-top .tooltip-arrow::before {
        border-top-color: #ffffff !important;
    }
</style>



<div class="container-fluid d-flex flex-column justify-content-start">
    <div class="cover-wrapper ">
        <div class="cover-wrapper bg-white shadow-sm rounded-3 w-100">
            <div class="cover-banner col-8 mx-auto">
                <div class="cover-photo-container" style="position: relative; width: 100%; max-height: 400px; overflow: hidden; border-bottom-left-radius: 10px; border-bottom-right-radius: 10px;">

                    <img src="/assets/images/Dropships.vn.gif"

                        alt="Ảnh bìa"
                        style="width: 100%; object-fit: cover;">
                    <div class="position-absolute top-0 end-0 m-2">
                        <label for="cover-img-upload" class="btn btn-light btn-sm rounded-circle shadow">
                            <i class="ri-camera-fill"></i>
                        </label>
                        <input type="file" id="cover-img-upload" class="d-none" />
                    </div>
                    <div class="position-absolute bottom-0 end-0 m-2 text-end">
                        <div class="bg-white bg-info px-2 py-1 rounded mb-1">
                            <a href="https://guitardongtam.com/" target="_blank">
                                Đến shop </a>
                        </div>
                        <div class="bg-white bg-opacity-75 px-2 py-1 rounded text-muted small">
                            Được tài trợ
                        </div>
                    </div>
                </div>

            </div>
            <div class="d-flex align-items-start gap-3 mt-3 col-8 mx-auto ps-4">
                <!-- Avatar -->
                <div class="position-relative " style="width: 150px; height: 150px;margin-top: -50px;">
                    <img src="@if (Auth::check() && Auth::user()->image)
                                    {{ Auth::user()->image }}
                                    @else 
                                    https://img.icons8.com/ios-filled/100/user-male-circle.png
                                    @endif" alt="Avatar" style=" height: 100%; object-fit: cover; border-radius: 50%; border: 4px solid #fff;">
                    <!-- Chấm xanh trạng thái -->
                    <span class="position-absolute bottom-0 end-0 translate-middle p-2 bg-success border border-light rounded-circle" style="width: 16px; height: 16px;"></span>
                </div>

                <!-- Tên và lượt thích -->
                <div>
                    <h4 class="mb-1 fw-bold d-flex align-items-center">
                        <h5 class="fs-16 mb-1 fw-bold">
                            {{ Auth::user()->name }}
                            @if(in_array(Auth::user()->name, ['Bùi Quốc Vũ', 'Vân', 'Trần Hoàng']))
                            <i class="ri-verified-badge-fill text-secondary ms-2" data-bs-toggle="tooltip" title="Nhà bán chính thức"></i>
                            @else
                            <i class="ri-verified-badge-fill text-muted ms-2" data-bs-toggle="tooltip" title="Nhà bán dropship"></i>
                            @endif
                        </h5>
                    </h4>
                    <div class="text-muted small">
                        <p class="text-muted mb-0"> Mã Code: {{ Auth::user()->referral_code }}</p>
                        <span class="badge bg-success-subtle text-success" role="button"
                            data-bs-toggle="modal" data-bs-target="#editProfileModal">
                            <i class="ri-edit-2-line me-1"></i> Chỉnh sửa
                        </span>
                        <!-- Modal cập nhật hồ sơ -->
                        <div class="modal fade" id="editProfileModal" tabindex="-1" aria-labelledby="editProfileModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editProfileModalLabel">Cập nhật hồ sơ</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <form action="{{ route('update-profile') }}" method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                <!-- Cột 7: Thông tin -->
                                                <div class="col-7">
                                                    <div class="mb-3">
                                                        <label for="name" class="form-label">Tên:</label>
                                                        <input type="text" class="form-control" id="name" name="name" value="{{ auth()->user()->name }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="email" class="form-label">Email:</label>
                                                        <input type="email" class="form-control" id="email" name="email" value="{{ auth()->user()->email }}" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label for="image" class="form-label">Chọn ảnh đại diện mới:</label>
                                                        <input type="file" class="form-control" id="image" name="image">
                                                    </div>
                                                </div>

                                                <!-- Cột 5: Ảnh hiện tại + nút thay -->
                                                <div class="col-5 text-center d-flex flex-column align-items-center justify-content-between">
                                                    @if(auth()->user()->image)
                                                    <div class="mb-3">
                                                        <label class="form-label">Ảnh đại diện hiện tại:</label>
                                                        <div class="border p-2 rounded-circle overflow-hidden" style="width: 100px; height: 100px;">
                                                            <img src="{{ auth()->user()->image }}" alt="Avatar" width="100" class="img-fluid rounded-circle">
                                                        </div>
                                                    </div>
                                                    @endif

                                                    <!-- Nút Thay ảnh -->
                                                    <button type="submit" class="btn btn-outline-primary mt-auto">
                                                        <i class="ri-camera-fill me-1"></i> Cập nhật
                                                    </button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-8  p-2">
                    <div class="row g-2">
                        @foreach($shops as $shop)
                        <div class="col-xl-6 col-md-6 ">
                            <div class="card card-height-100  bg-light">
                                <div class="card-body">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="avatar-sm flex-shrink-0">
                                            @if($shop->platform == 'Tiktok')
                                            <img src="https://img.icons8.com/ios-filled/250/tiktok--v1.png" alt="" style="width: 30px; height: 30px;">
                                            @elseif($shop->platform == 'Shoppe')
                                            <img src="https://img.icons8.com/fluency/240/shopee.png" alt="" style="width: 30px; height: 30px;">
                                            @else
                                            <i class="fas fa-store me-1"></i>
                                            @endif
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h4 class="fs-4 mb-1">{{ $shop->shop_name }}</h4>
                                            <p class="text-muted mb-0">{{ number_format($shop->revenue, 0, ',', '.') }} VNĐ</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="body-wrapper mt-3 d-flex justify-content-start col-10 mx-auto gap-3">
        <div class=" col-3 body-info bg-white shadow-sm rounded-3 p-3">
            <div class="body-content col-12">
                <div class="info-title">
                    <h4 class="mb-1 fw-bold">Giới thiệu</h4>
                </div>
                <div class="bg-white p-3">
                    <div class="mb-2 d-flex align-items-start">
                        <i class="ri-information-line me-2 fs-5 text-muted"></i>
                        <div>
                            <strong>Store</strong> · Cửa hàng quần áo nữ
                        </div>
                    </div>

                    <div class="mb-2 d-flex align-items-start">
                        <i class="ri-map-pin-line me-2 fs-5 text-muted"></i>
                        <div>
                            123 Tân Sơn, Phường 12, Quận Gò Vấp, Hồ Chí Minh, Ho Chi Minh City, Vietnam
                        </div>
                    </div>

                    <div class="d-flex align-items-start">
                        <i class="ri-phone-line me-2 fs-5 text-muted"></i>
                        <div>
                            093 4584 939
                        </div>
                    </div>
                </div>
            </div>
            <hr>
        </div>











        <div class=" col-9 body-info bg-white shadow-sm rounded-3 p-3 gap-3">
            <div class="body-content col-12 mx-auto ">
                <div class="row g-3">
                    @foreach($topProducts as $product)
                    <div class="col-md-3">
                        <div class="card h-100 d-flex flex-column" style="height: 330px;">
                            <div style="width: 100%; aspect-ratio: 1 / 1; overflow: hidden; border-top-left-radius: 0.5rem; border-top-right-radius: 0.5rem;">
                                <img src="{{ $product->image }}" class="card-img-top" alt="Ảnh sản phẩm {{ $product->sku }}" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                            <div class="card-body d-flex flex-column justify-content-between" style="display: flex; flex-direction: column; justify-content: space-between;">
                                <p class="card-text mb-1" style="font-size: 13px;"><strong>Mã SP:</strong> {{ $product->sku }}</p>
                                <h5 class="card-title mb-2" style="font-size:14px; overflow:hidden; text-overflow:ellipsis; display:-webkit-box; -webkit-line-clamp:2; -webkit-box-orient:vertical; line-height:1.2rem; min-height:2.4rem; margin-bottom:0.5rem;">
                                    {{ $product->product_name }}
                                </h5>
                                <div class="d-flex justify-content-between align-items-center mt-auto">
                                    <span class="me-3">Đơn hàng</span>
                                    <small class="text-muted">Lượt bán: {{ $product->total_quantity }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="d-flex align-items-center gap-3 mt-3 col-8 mx-auto ps-5 pb-5">
            </div>
        </div>
    </div>
</div>

</div>
















<!-- Hiển thị tooltip -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function(tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>

@endsection