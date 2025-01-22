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
                                            <th class="sort" data-sort="date">Ngày tạo đơn</th>
                                            <th class="sort" data-sort="product_code">SKU</th>
                                            <th class="sort" data-sort="product_name">Sản phẩm</th>
                                            <th class="sort" data-sort="product_cost">Giá sỉ</th>
                                            <th class="sort" data-sort="phidrop">Phí drop</th>
                                            <th class="sort" data-sort="doanhso">Doanh số</th>
                                            <th class="sort" data-sort="loinhuan">Lợi nhuận</th>
                                            <th class="sort" data-sort="shipment_code">Mã vận đơn</th>
                                            <th class="sort" data-sort="shop_name">Shop</th>
                                            <th class="sort" data-sort="hanhdong">Hành động</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        <tr>
                                            <th scope="row">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" name="checkAll" value="option1">
                                                </div>
                                            </th>
                                            <td class="id"><a href="apps-ecommerce-order-details.html" class="fw-medium link-primary">579789654924953800</a><br>
                                                <span class="badge bg-success">Mới</span>
                                            </td>
                                            <td class="date">07:37:43 22/12/2024</td>
                                            <td class="product_code">VAY196_DEN</td>
                                            <td class="product_name">Đầm Body Nữ Sát Nách Dáng Ngắn</td>
                                            <td class="customer_cost">35,500 đ</td>
                                            <td class="phidrop">5,000 đ</td>
                                            <td class="doanhso">115,000 đ</td>
                                            <td class="loinhuan text-success">47,500 đ</td>
                                            <td class="shipment_code">851257621313</td>
                                            <td class="shop_name"><b>lovito.vn</b></td>
                                            <td>
                                                <ul class="list-inline hstack gap-2 mb-0  d-flex justify-content-center">
                                                    <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Xem">
                                                        <a href="apps-ecommerce-order-details.html" class="text-primary d-inline-block">
                                                            <i class="ri-eye-fill fs-16"></i>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </td>
                                        </tr>
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