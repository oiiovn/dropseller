@extends('layout')
@section('title', 'main')

@section('main')

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
            <h4 class="mb-sm-0">Đơn hàng</h4>

            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">Thương mại điện tử</a></li>
                    <li class="breadcrumb-item active">Đơn hàng</li>
                </ol>
            </div>

        </div>
    </div>
</div>

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
                        <div class="d-flex gap-1 flex-wrap">
                            <button type="button" class="btn btn-success add-btn" data-bs-toggle="modal" id="create-btn" data-bs-target="#showModal"><i class="ri-add-line align-bottom me-1"></i> Tạo đơn hàng</button>
                            <button type="button" class="btn btn-info"><i class="ri-file-download-line align-bottom me-1"></i> Nhập</button>
                            <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                        </div>
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
                                    <option value="">Trạng thái</option>
                                    <option value="all" selected>Tất cả</option>
                                    <option value="Pending">Đang chờ</option>
                                    <option value="Inprogress">Đang xử lý</option>
                                    <option value="Cancelled">Đã hủy</option>
                                    <option value="Pickups">Đang nhận</option>
                                    <option value="Returns">Đã trả hàng</option>
                                    <option value="Delivered">Đã giao</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-xxl-2 col-sm-4">
                            <div>
                                <select class="form-control" data-choices data-choices-search-false name="choices-single-default" id="idPayment">
                                    <option value="">Phương thức thanh toán</option>
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
                            <thead class="text-muted table-light">
                                <tr class="text-uppercase">
                                    <th scope="col" style="width: 25px;">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                        </div>
                                    </th>
                                    <th class="sort" data-sort="id">Mã đơn hàng</th>
                                    <th class="sort" data-sort="customer_name">Khách hàng</th>
                                    <th class="sort" data-sort="product_name">Sản phẩm</th>
                                    <th class="sort" data-sort="date">Ngày đặt</th>
                                    <th class="sort" data-sort="amount">Tổng tiền</th>
                                    <th class="sort" data-sort="payment">Phương thức thanh toán</th>
                                    <th class="sort" data-sort="status">Trạng thái giao hàng</th>
                                    <th class="sort" data-sort="city">Hành động</th>
                                </tr>
                            </thead>
                            <tbody class="list form-check-all">
                                <tr>
                                    <th scope="row">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="checkAll" value="option1">
                                        </div>
                                    </th>
                                    <td class="id"><a href="apps-ecommerce-order-details.html" class="fw-medium link-primary">#VZ2101</a></td>
                                    <td class="customer_name">Frank Hook</td>
                                    <td class="product_name">Áo thun Puma</td>
                                    <td class="date">20 Tháng 12, 2021, <small class="text-muted">02:21 AM</small></td>
                                    <td class="amount">$654</td>
                                    <td class="payment">Mastercard</td>
                                    <td class="status"><span class="badge bg-warning-subtle text-warning text-uppercase">Đang chờ</span>
                                    </td>
                                    <td>
                                        <ul class="list-inline hstack gap-2 mb-0">
                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Xem">
                                                <a href="apps-ecommerce-order-details.html" class="text-primary d-inline-block">
                                                    <i class="ri-eye-fill fs-16"></i>
                                                </a>
                                            </li>
                                            <li class="list-inline-item edit" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Sửa">
                                                <a href="#showModal" data-bs-toggle="modal" class="text-primary d-inline-block edit-item-btn">
                                                    <i class="ri-pencil-fill fs-16"></i>
                                                </a>
                                            </li>
                                            <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Xóa">
                                                <a class="text-danger d-inline-block remove-item-btn" data-bs-toggle="modal" href="#deleteOrder">
                                                    <i class="ri-delete-bin-5-fill fs-16"></i>
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
                <div class="modal fade" id="showModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header bg-light p-3">
                                <h5 class="modal-title" id="exampleModalLabel">&nbsp;</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng" id="close-modal"></button>
                            </div>
                            <form class="tablelist-form" autocomplete="off">
                                <div class="modal-body">
                                    <input type="hidden" id="id-field" />
                                    <div class="mb-3" id="modal-id">
                                        <label for="orderId" class="form-label">Mã</label>
                                        <input type="text" id="orderId" class="form-control" placeholder="Mã" readonly />
                                    </div>
                                    <div class="mb-3">
                                        <label for="customername-field" class="form-label">Tên khách hàng</label>
                                        <input type="text" id="customername-field" class="form-control" placeholder="Nhập tên" required />
                                    </div>
                                    <div class="mb-3">
                                        <label for="productname-field" class="form-label">Sản phẩm</label>
                                        <select class="form-control" data-trigger name="productname-field" id="productname-field" required>
                                            <option value="">Chọn sản phẩm</option>
                                            <option value="Puma Tshirt">Áo thun Puma</option>
                                            <option value="Adidas Sneakers">Giày Adidas</option>
                                            <option value="350 ml Glass Grocery Container">Hộp đựng 350 ml</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label for="date-field" class="form-label">Ngày đặt</label>
                                        <input type="date" id="date-field" class="form-control" required placeholder="Chọn ngày" />
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Đóng</button>
                                    <button type="submit" class="btn btn-success">Thêm đơn hàng</button>
                                </div>
                            </form>
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
</div>
<!-- End Page-content -->





<!-- JAVASCRIPT -->
<script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="assets/libs/simplebar/simplebar.min.js"></script>
<script src="assets/libs/node-waves/waves.min.js"></script>
<script src="assets/libs/feather-icons/feather.min.js"></script>
<script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
<script src="assets/js/plugins.js"></script>

<!-- list.js min js -->
<script src="assets/libs/list.js/list.min.js"></script>

<!--list pagination js-->
<script src="assets/libs/list.pagination.js/list.pagination.min.js"></script>

<!-- ecommerce-order init js -->
<script src="assets/js/pages/ecommerce-order.init.js"></script>

<!-- Sweet Alerts js -->
<script src="assets/libs/sweetalert2/sweetalert2.min.js"></script>

<!-- App js -->
<script src="assets/js/app.js"></script>

</body>

@endsection