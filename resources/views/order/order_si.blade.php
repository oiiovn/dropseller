@extends('layout')
@section('title', 'main')

@section('main')
<div class="container-fluid">
    <!-- end page title -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="orderList">
                <div class="card-header border-0">
                    <div class="row align-items-center gy-3">
                        <div class="col-sm">
                            <h5 class="card-title mb-0">Lịch sử đơn hàng</h5>
                        </div>
                        <div class="col-sm-auto">
                        </div>
                    </div>
                </div>
                <div class="card-body border border-dashed border-end-0 border-start-0">
                    <form>
                        <div class="row g-3">
                            <div class="col-xxl-5 col-sm-6">
                                <div class="search-box">
                                    <input type="text" class="form-control search" placeholder="Tìm kiếm theo mã đơn hàng, khách hàng, trạng thái đơn hàng hoặc thông tin khác...">
                                    <i class="ri-search-line search-icon"></i>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-sm-6">
                                <div>
                                    <input type="text" class="form-control" data-provider="flatpickr" data-date-format="d M, Y" data-range-date="true" id="demo-datepicker" placeholder="Chọn ngày">
                                </div>
                            </div>
                            <div class="col-xxl-2 col-sm-4">
                                <div>
                                    <select class="form-control" data-choices data-choices-search-false name="choices-single-default" id="idStatus">
                                        <option value="all" selected>Tất cả đơn</option>
                                        <option value="Pending">Chờ xác nhận</option>
                                        <option value="Inprogress">Đơn mới</option>
                                        <option value="Cancelled">Đang đóng gói</option>
                                        <option value="Pickups">Đang nhận</option>
                                        <option value="Returns">Đã trả hàng</option>
                                        <option value="Delivered">Đã giao</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-2 col-sm-4">
                                <div>
                                    <select class="form-control" data-choices data-choices-search-false name="choices-single-default" id="idPayment">
                                        <option value="all" selected>Tất cả</option>
                                        <option value="Mastercard">Mastercard</option>
                                        <option value="Paypal">Paypal</option>
                                        <option value="Visa">Visa</option>
                                        <option value="COD">COD</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-xxl-1 col-sm-4">
                                <div>
                                    <button type="button" class="btn btn-primary w-100" onclick="SearchData();"> <i class="ri-equalizer-fill me-1 align-bottom"></i>
                                        Lọc
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="card-body pt-0">
                    <div>
                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active All py-3" data-bs-toggle="tab" id="All" href="#home1" role="tab" aria-selected="true">
                                    <i class="ri-store-2-fill me-1 align-bottom"></i> Tất cả đơn hàng
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 Delivered" data-bs-toggle="tab" id="Delivered" href="#delivered" role="tab" aria-selected="false">
                                    <i class="ri-checkbox-circle-line me-1 align-bottom"></i> Đã giao
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 Pickups" data-bs-toggle="tab" id="Pickups" href="#pickups" role="tab" aria-selected="false">
                                    <i class="ri-truck-line me-1 align-bottom"></i> Đang nhận hàng <span class="badge bg-danger align-middle ms-1">2</span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 Returns" data-bs-toggle="tab" id="Returns" href="#returns" role="tab" aria-selected="false">
                                    <i class="ri-arrow-left-right-fill me-1 align-bottom"></i> Đã trả hàng
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 Cancelled" data-bs-toggle="tab" id="Cancelled" href="#cancelled" role="tab" aria-selected="false">
                                    <i class="ri-close-circle-line me-1 align-bottom"></i> Đã hủy
                                </a>
                            </li>
                        </ul>
                        <div class="table-responsive table-card mb-1">
                            <table class="table table-nowrap align-middle" id="orderTable">
                                <thead class="text-muted table-light ">
                                    <tr class="text-uppercase ">
                                        <th scope="col" style="width: 25px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                            </div>
                                        </th>
                                        <th class="sort" data-sort="id">Mã đơn hàng</th>
                                        <th class="sort" data-sort="shop_name">Shop</th>
                                        <th class="sort" data-sort="date">Ngày tạo đơn</th>
                                        <th class="sort" data-sort="doanhso">Tổng số sản phẩm</th>
                                        <th class="sort" data-sort="phidrop">Phí drop</th>
                                        <th class="sort" data-sort="product_cost">Bill sỉ</th>
                                        <th class="sort" data-sort="shop_name">Trạng thái thanh toán</th>
                                        <th class="sort" data-sort="shop_name">Mã thanh toán</th>
                                        <th class="sort" data-sort="hanhdong">Hành động</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    @foreach($orders as $item)
                                    <tr>


                                        <th scope="row">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" name="checkAll" value="option1">
                                            </div>
                                        </th>
                                        <td class="id"><a href="apps-ecommerce-order-details.html" class="fw-medium link-primary">{{$item->order_code}}</a><br>

                                        </td>
                                        <td class="customer_cost">
                                            {{ $item->shop->shop_name}}
                                        </td>
                                        <td class="date">{{$item->export_date}}</td>
                                        <td class="customer_cost">{{$item->total_products}}</td>
                                        <td class="product_name">{{ number_format($item->total_dropship, 0, ',', '.') }} VNĐ</td>
                                        <td class="product_code">{{ number_format($item->total_bill, 0, ',', '.') }} VNĐ</td>
                                        <td class="date">{{$item->payment_status}}</td>
                                        <td class="date">{{$item->payment_code}}</td>
                                        <td>
                                            <!-- Button trigger modal -->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                                Xem chi tiết
                                            </button>

                                            <!-- Modal -->
                                            <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                <div class="modal-dialog modal-xl ">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h5 class="modal-title" id="staticBackdropLabel">Chi tiết đơn hàng</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div style="overflow-x: auto; overflow-y: auto; max-height: 500px;">
                                                                <table class="table table-hover table-nowrap mb-0 bg-white">
                                                                    <thead style="position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">
                                                                        <tr class="bg-light">
                                                                            <th scope="col">Mã sản phẩm</th>
                                                                            <th scope="col">Tên sản phẩm</th>
                                                                            <th scope="col">Lượt bán (50)</th>
                                                                            <th scope="col">Giá sỉ</th>
                                                                            <th scope="col">Tổng giá</th>
                                                                            <th scope="col" style="max-width: 50px;">Hình ảnh</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr style="vertical-align: middle;">
                                                                            <td>SP001</td>
                                                                            <td>Sản phẩm 1</td>
                                                                            <td style="text-align: center;">10</td>
                                                                            <td>50,000 VNĐ</td>
                                                                            <td>500,000 VNĐ</td>
                                                                            <td><img src="https://via.placeholder.com/50" alt="Hình ảnh" style="width: 50px; height: auto; border-radius: 10px;"></td>
                                                                        </tr>
                                                                        <tr style="vertical-align: middle;">
                                                                            <td>SP002</td>
                                                                            <td>Sản phẩm 2</td>
                                                                            <td style="text-align: center;">20</td>
                                                                            <td>100,000 VNĐ</td>
                                                                            <td>2,000,000 VNĐ</td>
                                                                            <td><img src="https://via.placeholder.com/50" alt="Hình ảnh" style="width: 50px; height: auto; border-radius: 10px;"></td>
                                                                        </tr>
                                                                        <tr style="vertical-align: middle;">
                                                                            <td>SP003</td>
                                                                            <td>Sản phẩm 3</td>
                                                                            <td style="text-align: center;">15</td>
                                                                            <td>70,000 VNĐ</td>
                                                                            <td>1,050,000 VNĐ</td>
                                                                            <td>Không có hình ảnh</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>

                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>


                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="noresult" style="display: none">
                                <div class="text-center">
                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:75px;height:75px"></lord-icon>
                                    <h5 class="mt-2">Rất tiếc! Không tìm thấy kết quả</h5>
                                    <p class="text-muted">Chúng tôi đã tìm kiếm hơn 150+ đơn hàng nhưng không thấy kết quả phù hợp.</p>
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <div class="pagination-wrap hstack gap-2">
                                <a class="page-item pagination-prev disabled" href="#">
                                    Trước
                                </a>
                                <ul class="pagination listjs-pagination mb-0"></ul>
                                <a class="page-item pagination-next" href="#">
                                    Tiếp theo
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--end row-->

</div>
<!-- container-fluid -->



@endsection