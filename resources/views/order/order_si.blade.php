@extends('layout')
@section('title', 'main')

@section('main')
<div class="container-fluid">
    <!-- end page title -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="orderList">
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
                                        <option value="all" selected>Thanh toán</option>
                                        <option value="Pending">Đã thanh toán</option>
                                        <option value="Pending">Chưa thanh toán</option>
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
                                    <i class=" me-1 align-bottom"></i> lovito.vn
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link py-3 Pickups" data-bs-toggle="tab" id="Pickups" href="#pickups" role="tab" aria-selected="false">
                                    <i class=" me-1 align-bottom"></i> diva.vn </span>
                                </a>
                            </li>
                        </ul>
                        <div class="table-responsive table-card mb-1">
                            <table class="table table-nowrap align-middle table-hover " id="orderTable">
                                <thead class="text-muted table-light ">
                                    <tr class="text-uppercase ">
                                        <th scope="col" style="width: 25px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="checkAll" value="option">
                                            </div>
                                        </th>
                                        <th class="sort" data-sort="id">Mã đơn nhập hàng</th>
                                        <th class="sort" data-sort="shop_name">Shop</th>
                                        <th class="sort" data-sort="date">Ngày tạo đơn</th>
                                        <th class="sort" data-sort="doanhso">Số lượng</th>
                                        <th class="sort" data-sort="phidrop">Phí drop</th>
                                        <th class="sort" data-sort="product_cost">Tổng Bill</th>
                                        <th class="sort" data-sort="shop_name">Thanh toán</th>
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
                                        <td class="id">
                                            <ul style="list-style: none; padding: 0; margin: 0;">
                                                <li>
                                                    <a class="fw-medium link-primary">{{$item->order_code}}</a>
                                                </li>
                                                <li>
                                                    <a style="font-size: 12px;">{{$item->filter_date}}</a>
                                                </li>
                                            </ul>
                                        <td class="customer_cost" data-shop-id="{{ $item->shop->id ?? 0 }}">
                                            {{ $item->shop->shop_name ?? 'N/A' }}
                                        </td>
                                        </td>
                                        <td class="date">{{$item->export_date}}</td>
                                        <td class="customer_cost">{{$item->total_products}}</td>
                                        <td class="product_name">{{ number_format($item->total_dropship, 0, ',', '.') }} đ</td>
                                        <td class="product_code">{{ number_format($item->total_bill, 0, ',', '.') }} đ</td>
                                        <td class="date">{{$item->payment_status}}</td>
                                        <td class="date">{{$item->payment_code}}</td>
                                        <td>
                                            <ul class="list-inline hstack gap-2 mb-0 d-flex justify-content-center">
                                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Xem chi tiết">
                                                    <a href="#" class="text-primary d-inline-block" data-bs-toggle="modal" data-bs-target="#staticBackdrop-{{$item->id}}">
                                                        <i class="ri-eye-fill fs-16"></i>
                                                    </a>
                                                </li>
                                            </ul>
                                            <!-- Modal -->
                                            <div class="modal fade" id="staticBackdrop-{{$item->id}}" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                                <div class="modal-dialog" style="max-width: 70%; width: 100%;">
                                                    <div class="modal-content">
                                                        <div class="modal-header">
                                                            <h6 class="modal-title" id="staticBackdropLabel">ID Đơn Hàng: {{ $item->order_code }}</h6>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                        </div>
                                                        <!-- Phần chi tiết sản phẩm -->
                                                        <div class="modal-body" style="display: flex; gap: 20px; overflow-x: auto; max-height: 800px;">
                                                            <div class="col-xl-9" style="flex: 0 0 70%;">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <div class="table-responsive table-card">
                                                                            <div class="table-responsive" style="max-height: 800px; overflow-y: auto;">
                                                                                <table class="table table-nowrap align-middle table-borderless mb-0 table-hover ">
                                                                                    <thead class="table-light text-muted">
                                                                                        <tr>
                                                                                            <th scope="col" style="position: sticky; top: 0; background: #f8f9fa; z-index: 2;">Sản Phẩm</th>
                                                                                            <th scope="col" style="position: sticky; top: 0; background: #f8f9fa; z-index: 2;">Số Lượng</th>
                                                                                            <th scope="col" style="position: sticky; top: 0; background: #f8f9fa; z-index: 2;">Giá Nhập</th>
                                                                                            <th scope="col" class="text-end" style="position: sticky; top: 0; background: #f8f9fa; z-index: 2;">Tổng Giá Nhập</th>
                                                                                        </tr>
                                                                                    </thead>
                                                                                    <tbody>
                                                                                        @foreach($item->orderDetails as $detail)
                                                                                        <tr>
                                                                                            <td>
                                                                                                <div class="d-flex">
                                                                                                    <div class="flex-shrink-0 avatar-md bg-light rounded p-1">
                                                                                                        <img src="{{$detail->image}}" alt="" class="img-fluid d-block">
                                                                                                    </div>
                                                                                                    <div class="flex-grow-1 ms-3">
                                                                                                        <h5 class="fs-13">
                                                                                                            <a>{{$detail->product_name}}</a>
                                                                                                        </h5>
                                                                                                        <p class="text-muted mb-0 fs-11">SKU: <span class="fw-medium">{{$detail->sku}}</span></p>
                                                                                                    </div>
                                                                                                </div>
                                                                                            </td>
                                                                                            <td>{{$detail->quantity}}</td>
                                                                                            <td>{{ number_format($detail->unit_cost, 0, ',', '.') }} đ</td>
                                                                                            <td class="text-end">{{ number_format($detail->total_cost, 0, ',', '.') }} đ</td>
                                                                                        </tr>
                                                                                        @endforeach
                                                                                    </tbody>
                                                                                </table>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Phần thanh toán tổng -->
                                                            <div class="col-xl-4" style="flex: 0 0 25%; position: sticky; top: 0;">
                                                                <div class="card">
                                                                    <div class="card-body">
                                                                        <table class="table table-borderless mb-0">
                                                                            <tbody>
                                                                                <tr>
                                                                                    <td>Tổng số sản phẩm :</td>
                                                                                    <td class="text-end">{{ $item->total_products}} </td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Tổng phí Dropship :</td>
                                                                                    <td class="text-end">{{ number_format($item->total_dropship, 0, ',', '.') }} đ</td>
                                                                                </tr>
                                                                                <tr>
                                                                                    <td>Tổng tiền nhập hàng :</td>
                                                                                    <td class="text-end">{{ number_format($item->total_bill-$item->total_dropship, 0, ',', '.') }} đ</td>
                                                                                </tr>
                                                                                <tr class="border-top border-top-dashed">
                                                                                    <th scope="row">Tổng tiền đơn hàng (đ) :</th>
                                                                                    <th class="text-end">{{ number_format($item->total_bill, 0, ',', '.') }} đ</th>
                                                                                </tr>
                                                                            </tbody>
                                                                        </table>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                        </div>
                    </div>
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
<script>
    document.querySelectorAll('.customer_cost').forEach(td => {
        const shopId = td.dataset.shopId; // Gắn shopId vào dataset
        if (shopId) {
            const color = `#${((parseInt(shopId) * 1234567) & 0xFFFFFF).toString(16).padStart(6, '0')}`;
            td.style.color = color;
        }
    });
</script>
<script>
    // Copy mã order code 
    function copyOrderCode(iconElement) {
        try {
            // Lấy mã order_code từ thẻ <a>
            const orderCode = iconElement.previousElementSibling.getAttribute('data-order-code');

            if (!orderCode) {
                alert("No order code found!");
                return;
            }

            // Tạo textarea tạm để sao chép
            const tempInput = document.createElement('textarea');
            tempInput.value = orderCode;
            document.body.appendChild(tempInput);

            // Sao chép nội dung
            tempInput.select();
            document.execCommand('copy');

            // Xóa textarea tạm
            document.body.removeChild(tempInput);

            // Thông báo sao chép thành công
            alert(`Copied: ${orderCode}`);
        } catch (error) {
            console.error("Error copying order code: ", error);
            alert("Failed to copy order code!");
        }
    }
</script>



@endsection