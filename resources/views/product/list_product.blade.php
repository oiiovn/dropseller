@extends('layout')
@section('title', 'main')
@section('main')
<div class="container-fluid">
    <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#all-products" type="button" role="tab" aria-controls="all-products" aria-selected="true">Đăng sản phẩm</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="pending-tab" data-bs-toggle="tab" data-bs-target="#pending-products" type="button" role="tab" aria-controls="pending-products" aria-selected="false">Sản phẩm kho Drop</button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="all-products" role="tabpanel" aria-labelledby="home-tab">
            <div class="container-fluid bg-white">
                <div class="row bg-white pt-3">
                    <div class="col-xl-12 bg-white d-flex">
                        <div>
                            <p> các tab chức năng đăng lên tiktok shop </p>
                        </div>
                    </div>
                </div>
                <div class="row pt-3">
                    <div class="col-xl-12">
                        <div class="card " style="border: 1px rgb(236, 236, 236) solid">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm</h4>
                            </div><!-- end card header -->
                            <div class="card-body">
                                {{-- <p class="text-muted mb-4">Use .<code>table-striped-columns</code> to add zebra-striping to any table column.</p> --}}
                                <div class="live-preview">
                                    <div class="table-responsive table-card">
                                        <table class="table align-middle table-nowrap table-striped-columns mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th scope="col" style="width: 46px;">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="" id="cardtableCheck">
                                                            <label class="form-check-label" for="cardtableCheck"></label>
                                                        </div>
                                                    </th>
                                                    <th scope="col">Ảnh</th>
                                                    <th scope="col">Tên sản phẩm</th>
                                                    <th scope="col">Đã đăng lên</th>
                                                    <th scope="col">Giá</th>
                                                    <th scope="col">Tồn kho</th>
                                                    <th scope="col">Trạng thái</th>
                                                    <th scope="col">Kho sỉ</th>
                                                    <th scope="col" style="width: 150px;">Hành động</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td>
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" value="" id="cardtableCheck01">
                                                            <label class="form-check-label" for="cardtableCheck01"></label>
                                                        </div>
                                                    </td>
                                                    <td>Ảnh</td>
                                                    <td>Tên</td>
                                                    <td>lovito</td>
                                                    <td>10000</td>
                                                    <td>100</td>
                                                    <td>hoạt động</td>
                                                    <td>Beveda</td>
                                                    <td>
                                                        <div class="hstack gap-3 flex-wrap">
                                                            <a href="javascript:void(0);" class="link-success fs-15"><i class="ri-edit-2-line"></i></a>
                                                            <a href="javascript:void(0);" class="link-danger fs-15"><i class="ri-delete-bin-line"></i></a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div><!-- end card-body -->
                        </div><!-- end card -->
                    </div><!-- end col -->
                </div><!-- end row -->
            </div>
        </div>
        <div class="tab-pane fade" id="pending-products" role="tabpanel" aria-labelledby="pending-tab">
            <div class="tab-pane fade show active" id="all-products" role="tabpanel" aria-labelledby="home-tab">
                <div class="container-fluid bg-white">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-8 col-sm-12 mb-3">
                                <div class="card d-flex flex-row align-items-center p-3 shadow-lg" style="max-width: 300px; top: 10px;">
                                    <div class="me-3">
                                        <img src="https://down-tx-vn.img.susercontent.com/vn-11134216-7r98o-lse2pcm5qcuxe0_tn.webp" alt="" class="rounded-circle" style="width: 50px; height: 50px; object-fit: cover;">
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">Beveda
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="20" height="20" style="margin-left: 5px;">
                                                <circle cx="12" cy="12" r="12" fill="#ff0000" />
                                                <path d="M10.5 15.5L7 12l1.4-1.4 2.1 2.1 4.9-4.9L16 9.6l-5.5 5.9z" fill="white" />
                                            </svg>
                                        </div>
                                        <span class="badge bg-info">Tồn kho tốt</span>
                                        <span class="badge bg-warning text-dark">Trên 1.000 + sản phẩm</span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6 d-flex justify-content-center align-items-start">
                                <div class="d-flex justify-content-end p-2">
                                    <select class="form-select bg-info" style="max-width: 200px;">
                                        <option selected>Đăng lên nền tảng</option>
                                        <option value="shopee">Shopee</option>
                                        <option value="tiktok">TikTok</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2 col-sm-6">
                                <div>
                                    <button class="btn btn-primary px-4" style="position: absolute; top: 10px; right: 10px; white-space: nowrap;">Lưu sản phẩm</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div>
                    </div>
                    <div class="row pt-3">
                        <div class="col-xl-12">
                            <div class="card " style="border: 1px rgb(236, 236, 236) solid">
                                <div class="card-header align-items-center d-flex">
                                    <h4 class="card-title mb-0 flex-grow-1">Danh sách sản phẩm</h4>
                                </div><!-- end card header -->
                                <div class="card-body">
                                    <!-- {{-- <p class="text-muted mb-4">Use .<code>table-striped-columns</code> to add zebra-striping to any table column.</p> --}} -->
                                    <div class="live-preview">
                                        <div class="table-responsive table-card">
                                            <table class="table align-middle table-nowrap table-striped-columns mb-0">
                                                <thead class="table-light">
                                                    <tr>
                                                        <th scope="col" style="width: 46px;">
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="cardtableCheck">
                                                                <label class="form-check-label" for="cardtableCheck"></label>
                                                            </div>
                                                        </th>
                                                        <th scope="col">Ảnh</th>
                                                        <th scope="col">Tên sản phẩm</th>
                                                        <th scope="col">Trạng thái</th>
                                                        <th scope="col">Giá</th>
                                                        <th scope="col">Tồn kho</th>
                                                        <th scope="col">Tạo lúc</th>
                                                        <th scope="col">Nền tảng</th>
                                                        <th scope="col" style="width: 150px;">Hành động</th>
                                                        <th scope="col" style="width: 150px;">Thao tác</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="cardtableCheck01">
                                                                <label class="form-check-label" for="cardtableCheck01"></label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <img src="https://storage.googleapis.com/salework_photos/1734507313553.jpeg" alt="Ảnh" style="width:70px; height:70px;">
                                                        </td>
                                                        <td>Váy Nhung Đính Đá Sang Trọng Thiết Kế Ôm Dáng, Chất Liệu Nhung Cao Cấp Dành Cho Người Dưới 53kg </td>
                                                        <td>Đang hoạt động</td>
                                                        <td>10000</td>
                                                        <td>100</td>
                                                        <td>22:47 - 19/12/2024</td>
                                                        <td>Tiktok</td>
                                                        <td><span class="badge bg-warning text-dark d-flex justify-content-center">Chưa lưu</span></td>

                                                        <td>
                                                            <div class="hstack gap-3 flex-wrap">
                                                                <a href="javascript:void(0);" class="link-success fs-15"><i class="ri-edit-2-line"></i></a>
                                                                <a href="javascript:void(0);" class="link-danger fs-15"><i class="ri-delete-bin-line"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="cardtableCheck01">
                                                                <label class="form-check-label" for="cardtableCheck01"></label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <img src="https://storage.googleapis.com/salework_photos/1734507313553.jpeg" alt="Ảnh" style="width:70px; height:70px;">
                                                        </td>
                                                        <td>Váy Nhung Đính Đá Sang Trọng Thiết Kế Ôm Dáng, Chất Liệu Nhung Cao Cấp Dành Cho Người Dưới 53kg </td>
                                                        <td>Đang hoạt động</td>
                                                        <td>10000</td>
                                                        <td>100</td>
                                                        <td>22:47 - 19/12/2024</td>
                                                        <td>Tiktok</td>
                                                        <td><span class="badge bg-warning text-dark d-flex justify-content-center">Chưa lưu</span></td>

                                                        <td>
                                                            <div class="hstack gap-3 flex-wrap">
                                                                <a href="javascript:void(0);" class="link-success fs-15"><i class="ri-edit-2-line"></i></a>
                                                                <a href="javascript:void(0);" class="link-danger fs-15"><i class="ri-delete-bin-line"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="cardtableCheck01">
                                                                <label class="form-check-label" for="cardtableCheck01"></label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <img src="https://storage.googleapis.com/salework_photos/1734507313553.jpeg" alt="Ảnh" style="width:70px; height:70px;">
                                                        </td>
                                                        <td>Váy Nhung Đính Đá Sang Trọng Thiết Kế Ôm Dáng, Chất Liệu Nhung Cao Cấp Dành Cho Người Dưới 53kg </td>
                                                        <td>Đang hoạt động</td>
                                                        <td>10000</td>
                                                        <td>100</td>
                                                        <td>22:47 - 19/12/2024</td>
                                                        <td>Tiktok</td>
                                                        <td><span class="badge bg-warning text-dark d-flex justify-content-center">Chưa lưu</span></td>

                                                        <td>
                                                            <div class="hstack gap-3 flex-wrap">
                                                                <a href="javascript:void(0);" class="link-success fs-15"><i class="ri-edit-2-line"></i></a>
                                                                <a href="javascript:void(0);" class="link-danger fs-15"><i class="ri-delete-bin-line"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="cardtableCheck01">
                                                                <label class="form-check-label" for="cardtableCheck01"></label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <img src="https://storage.googleapis.com/salework_photos/1734507313553.jpeg" alt="Ảnh" style="width:70px; height:70px;">
                                                        </td>
                                                        <td>Váy Nhung Đính Đá Sang Trọng Thiết Kế Ôm Dáng, Chất Liệu Nhung Cao Cấp Dành Cho Người Dưới 53kg </td>
                                                        <td>Đang hoạt động</td>
                                                        <td>10000</td>
                                                        <td>100</td>
                                                        <td>22:47 - 19/12/2024</td>
                                                        <td>Tiktok</td>
                                                        <td><span class="badge bg-warning text-dark d-flex justify-content-center">Chưa lưu</span></td>

                                                        <td>
                                                            <div class="hstack gap-3 flex-wrap">
                                                                <a href="javascript:void(0);" class="link-success fs-15"><i class="ri-edit-2-line"></i></a>
                                                                <a href="javascript:void(0);" class="link-danger fs-15"><i class="ri-delete-bin-line"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <div class="form-check">
                                                                <input class="form-check-input" type="checkbox" value="" id="cardtableCheck01">
                                                                <label class="form-check-label" for="cardtableCheck01"></label>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <img src="https://storage.googleapis.com/salework_photos/1734507313553.jpeg" alt="Ảnh" style="width:70px; height:70px;">
                                                        </td>
                                                        <td>Váy Nhung Đính Đá Sang Trọng Thiết Kế Ôm Dáng, Chất Liệu Nhung Cao Cấp Dành Cho Người Dưới 53kg </td>
                                                        <td>Đang hoạt động</td>
                                                        <td>10000</td>
                                                        <td>100</td>
                                                        <td>22:47 - 19/12/2024</td>
                                                        <td>Tiktok</td>
                                                        <td><span class="badge bg-success text-dark d-flex justify-content-center">Đã lưu</span></td>
                                                        <td>
                                                            <div class="hstack gap-3 flex-wrap">
                                                                <a href="javascript:void(0);" class="link-success fs-15"><i class="ri-edit-2-line"></i></a>
                                                                <a href="javascript:void(0);" class="link-danger fs-15"><i class="ri-delete-bin-line"></i></a>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                                <div class="align-items-center m-1  justify-content-between row text-center text-sm-start">
                                                    <div class="col-sm">
                                                        <div class="text-muted">
                                                            Hiển thị <span class="fw-semibold">5</span> trên tổng <span class="fw-semibold">15</span> Sản phẩm
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-auto  mt-3 mt-sm-0">
                                                        <ul class="pagination pagination-separated pagination-sm mb-0 justify-content-center">
                                                            <li class="page-item disabled">
                                                                <a href="#" class="page-link">←</a>
                                                            </li>
                                                            <li class="page-item">
                                                                <a href="#" class="page-link">1</a>
                                                            </li>
                                                            <li class="page-item active">
                                                                <a href="#" class="page-link">2</a>
                                                            </li>
                                                            <li class="page-item">
                                                                <a href="#" class="page-link">3</a>
                                                            </li>
                                                            <li class="page-item">
                                                                <a href="#" class="page-link">→</a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </table>
                                        </div>
                                    </div>
                                </div><!-- end card-body -->
                            </div><!-- end card -->
                        </div><!-- end col -->
                    </div><!-- end row -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection