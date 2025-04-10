@extends('layout')
@section('title', 'main')

@section('main')


<style>
    .hienthicopy .icon {
        display: none;
        cursor: pointer;
    }

    .hienthicopy:hover .icon {
        display: inline;
    }

    .table thead th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 2;
    }

    .search-box .clear-icon {
        position: absolute;
        right: 10px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        display: none;
    }

    .search-box input:valid~.clear-icon {
        display: inline;
    }
</style>


<div class="container-fluid" style=" width: 100%; background: white; ">
    <!-- end page title -->
    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="orderList">
                <div class="card-body pt-0">
                    <div>
                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active All py-3" data-bs-toggle="tab" id="All" href="#home1" role="tab" aria-selected="true">
                                    <i class="ri-store-2-fill me-1 align-bottom"></i> Tất cả giao dịch
                                </a>
                            </li>
                            @foreach($transactionsByReferral as $userId => $data)
                            <li class="nav-item">
                                <a class="nav-link py-3" data-bs-toggle="tab" id="user-{{$userId}}" href="#user-{{$userId}}-content" role="tab">
                                    {{ $data['user']->name }}
                                </a>
                            </li>
                            @endforeach
                        </ul>
                        <div class="tab-content">
                            <!-- Tất cả đơn hàng -->
                            <div class="tab-pane fade show active" id="home1" role="tabpanel">
                                <div class="table-responsive table-card mb-1">
                                    <table id="transaction_allll" class="table table-hover">
                                        <thead class="text-muted table-light ">
                                            <tr class="text-uppercase ">
                                                <th class="sort" data-sort="id">Tên Người chuyển</th>
                                                <th class="sort" data-sort="shop_name">Mã giao dịch</th>
                                                <th class="sort" data-sort="date">Nội dung</th>
                                                <th class="sort" data-sort="date">Số tiền</th>
                                                <th class="sort" data-sort="date">Thời gian nạp tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all text-black-50">
                                            @foreach($transactionsByReferral as $data)
                                            @foreach($data['transactions'] as $transaction)
                                            <tr>
                                                <td style="width:15%">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <img src="
                                                                @if(isset($data['user']->image) && !empty($data['user']->image))
                                                                    {{$data['user']->image }}
                                                                @else
                                                                    https://img.icons8.com/ios-filled/100/user-male-circle.png
                                                                @endif
                                                            " alt="" class="avatar-sm " style=" border-radius:10px" />
                                                        </div>
                                                        <div>
                                                            <h5 class="fs-14 my-1 fw-medium">
                                                                <a class="text-reset">{{ $data['user']->name }}</a>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="export_date" style="width:15%">{{$transaction->transaction_id}}</td>
                                                <td class="total_products" style="width:35%">{{$transaction->description}}</td>
                                                <td class="total_dropship" style="width:15%">
                                                @if ($transaction->type === 'IN')
                                                <span class="badge bg-secondary-subtle text-secondary badge-border">+{{ number_format($transaction->amount, 0, '.', ',') }} VNĐ</span>
                                                @elseif ($transaction->type === 'OUT')
                                                <span class="badge bg-danger-subtle text-danger badge-border">-{{ number_format($transaction->amount, 0, '.', ',') }} VNĐ</span>
                                                @else
                                                <span class="badge bg-secondary-subtle text-secondary badge-border">{{ number_format($transaction->amount, 0, '.', ',') }} VNĐ</span>
                                                @endif
                                                </td>
                                                <td class="total_products" style="width:15%">{{$transaction->created_at}}</td>
                                            </tr>
                                            @endforeach
                                            @endforeach
                                        </tbody>
                                    </table> 
                                    <script>
                                        $(document).ready(function() {
                                            $('#transaction_allll').DataTable({
                                                "paging": true, // Bật phân trang
                                                "searching": true, // Bật tìm kiếm
                                                "ordering": true, // Bật sắp xếp
                                                "info": true, // Hiển thị thông tin
                                                "lengthMenu": [10, 20, 50, 100, 150], // Số lượng dòng hiển thị
                                                "order": [
                                                    [4, "desc"]
                                                ], // Mặc định sắp xếp cột thứ 3 (Ngày tạo đơn) theo mới nhất

                                                // Chỉnh Tiếng Việt
                                                "language": {
                                                    "lengthMenu": "Hiển thị _MENU_ hoá đơn",
                                                    "zeroRecords": "Không tìm thấy dữ liệu",
                                                    "info": "Hiển thị _START_ đến _END_ của _TOTAL_ hoá đơn",
                                                    "infoEmpty": "Không có dữ liệu để hiển thị",
                                                    "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                                                    "search": "🔍",
                                                    "paginate": {
                                                        "first": "Trang đầu",
                                                        "last": "Trang cuối",
                                                        "next": "Tiếp theo",
                                                        "previous": "Quay lại"
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                            <!-- Đơn hàng theo từng shop -->
                            @foreach($transactionsByReferral as $userId => $data)
                            <div class="tab-pane fade show " id="user-{{$userId}}-content" role="tabpanel">
                                <div class="table-responsive table-card mb-1">
                                    <table id="transactionall-{{$userId}}" class="table table-hover">
                                        <thead class="text-muted table-light ">
                                            <tr class="text-uppercase ">
                                                <th class="sort" data-sort="id">Tên Người chuyển</th>
                                                <th class="sort" data-sort="shop_name">Mã giao dịch</th>
                                                <th class="sort" data-sort="date">Nội dung</th>
                                                <th class="sort" data-sort="date">Số tiền</th>
                                                <th class="sort" data-sort="date">Thời gian nạp tiền</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all text-black-50">
                                            @foreach($data['transactions'] as $transaction)
                                            <tr>
                                                <td style="width:15%">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <img src="
                                                                @if(isset($data['user']->image) && !empty($data['user']->image))
                                                                    {{$data['user']->image }}
                                                                @else
                                                                    https://img.icons8.com/ios-filled/100/user-male-circle.png
                                                                @endif
                                                            " alt="" class="avatar-sm " style=" border-radius:10px" />
                                                        </div>
                                                        <div>
                                                            <h5 class="fs-14 my-1 fw-medium">
                                                                <a class="text-reset">{{ $data['user']->name }}</a>
                                                            </h5>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="export_date" style="width:15%">{{$transaction->transaction_id}}</td>
                                                <td class="total_products" style="width:35%">{{$transaction->description}}</td>
                                                <td> @if ($transaction->type === 'IN')
                                                <span class="badge bg-secondary-subtle text-secondary badge-border">+{{ number_format($transaction->amount, 0, '.', ',') }} VNĐ</span>
                                                @elseif ($transaction->type === 'OUT')
                                                <span class="badge bg-danger-subtle text-danger badge-border">-{{ number_format($transaction->amount, 0, '.', ',') }} VNĐ</span>
                                                @else
                                                <span class="badge bg-secondary-subtle text-secondary badge-border">{{ number_format($transaction->amount, 0, '.', ',') }} VNĐ</span>
                                                @endif</td>
                                                <td class="total_products" style="width:15%">{{$transaction->created_at}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <script>
                                        $(document).ready(function() {
                                            $('#transactionall-{{$userId}}').DataTable({
                                                "paging": true, // Bật phân trang
                                                "searching": true, // Bật tìm kiếm
                                                "ordering": true, // Bật sắp xếp
                                                "info": true, // Hiển thị thông tin
                                                "lengthMenu": [10, 20, 50, 100, 150], // Số lượng dòng hiển thị
                                                "order": [
                                                    [4, "desc"]
                                                ], // Mặc định sắp xếp cột thứ 3 (Ngày tạo đơn) theo mới nhất

                                                // Chỉnh Tiếng Việt
                                                "language": {
                                                    "lengthMenu": "Hiển thị _MENU_ hoá đơn",
                                                    "zeroRecords": "Không tìm thấy dữ liệu",
                                                    "info": "Hiển thị _START_ đến _END_ của _TOTAL_ hoá đơn",
                                                    "infoEmpty": "Không có dữ liệu để hiển thị",
                                                    "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                                                    "search": "🔍",
                                                    "paginate": {
                                                        "first": "Trang đầu",
                                                        "last": "Trang cuối",
                                                        "next": "Tiếp theo",
                                                        "previous": "Quay lại"
                                                    }
                                                }
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                            @endforeach
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- Include DataTables JS -->


<script>
    document.querySelectorAll('.customer_cost').forEach(td => {
        const shopId = td.dataset.shopId; // Gắn shopId vào dataset
        if (shopId) {
            const color = `#${((parseInt(shopId) * 1234567) & 0xFFFFFF).toString(16).padStart(6, '0')}`;
            td.style.color = color;
        }
    });
</script>
@endsection