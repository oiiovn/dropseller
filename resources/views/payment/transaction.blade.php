@extends('layout')
@section('title', 'main')

@section('main')
<div class="container-fluid bg-white p-3 " style="height: 84vh;">
    <!-- Tabs Navigation -->
    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link .bg-gradient active mx-2" id="pills-all-tab" data-bs-toggle="pill" data-bs-target="#pills-all" type="button" role="tab" aria-controls="pills-all" aria-selected="true">Tất cả giao dịch</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link .bg-warning mx-2" id="pills-bill-si-tab" data-bs-toggle="pill" data-bs-target="#pills-bill-si" type="button" role="tab" aria-controls="pills-bill-si" aria-selected="false">Giao dịch đơn sỉ</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link .bg-info mx-2" id="pills-nap-tab" data-bs-toggle="pill" data-bs-target="#pills-nap" type="button" role="tab" aria-controls="pills-nap" aria-selected="false">Nạp tiền</button>
        </li>
        <!-- <li class="nav-item" role="presentation">
            <button class="nav-link .bg-warning mx-2" id="pills-ads-tab" data-bs-toggle="pill" data-bs-target="#pills-ads" type="button" role="tab" aria-controls="pills-ads" aria-selected="false">Chi tiêu ADS</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link .bg-primary mx-2" id="pills-dich-vu-tab" data-bs-toggle="pill" data-bs-target="#pills-dich-vu" type="button" role="tab" aria-controls="pills-dich-vu" aria-selected="false">Hoá đơn dịch vụ</button>
        </li> -->
    </ul>

    <!-- Tabs Content -->
    <div class="tab-content" style="height: 85%; max-height: 1500px; overflow-y: auto; overflow-x: hidden;  " id="pills-tabContent">
        <!-- Tất cả giao dịch -->
        <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab">
            <div class="table-responsive">
                <table style="table-layout: fixed; width: 100%;" class="table table-nowrap " id="all">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID giao dịch</th>
                            <!-- <th scope="col">Ngân hàng</th>
                                    <th scope="col">Số tài khoản</th> -->
                            <th style="col">Nội dung</th>
                            <th scope="col">Ngày</th>
                            <th scope="col">Tổng tiền</th>
                            <th scope="col">Loại</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Transactions as $Transaction)
                        <tr>
                            <td>
                                {{$Transaction->transaction_id}}
                            </td>
                            <!-- <td><span class="badge bg-warning">{{$Transaction->bank}}</span></td>
                                    <td>{{$Transaction->account_number}}</td> -->
                            <td style="width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{$Transaction->description}}
                            </td>
                            <td>{{$Transaction->transaction_date}}</td>
                            <td>
                                @if ($Transaction->type === 'IN')
                                <span class="badge bg-secondary-subtle text-secondary badge-border">+{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                @elseif ($Transaction->type === 'OUT')
                                <span class="badge bg-danger-subtle text-danger badge-border">-{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                @else
                                <span class="badge bg-secondary-subtle text-secondary badge-border">{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                @endif
                            </td>

                            <td>
                                <span>
                                    @if ($Transaction->type === 'IN')
                                    <span class="badge rounded-pill border border-primary text-primary">Nạp số dư</span>
                                    @elseif ($Transaction->type === 'OUT')
                                    <span class="badge rounded-pill border border-danger text-danger">Chi số dư</span>
                                    @else
                                    <span>Unknown Type</span>
                                    @endif
                                </span>
                            </td>

                        </tr>

                        @endforeach

                    </tbody>
                </table>
                <script>
                    $(document).ready(function() {
                        $('#all').DataTable({
                            "paging": true, // Bật phân trang
                            "searching": true, // Bật tìm kiếm
                            "ordering": true, // Bật sắp xếp
                            "info": true, // Hiển thị thông tin
                            "lengthMenu": [10, 20, 50, 100,200], // Số lượng dòng hiển thị

                            // Chỉnh Tiếng Việt
                            "language": {
                                "lengthMenu": "Hiển thị _MENU_giao dịch",
                                "zeroRecords": "Không tìm thấy dữ liệu",
                                "info": "Hiển thị _START_ đến _END_ của _TOTAL_ giao dịch",
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
        <!-- Giao dịch đơn sỉ -->
        <div class="tab-pane fade" id="pills-bill-si" role="tabpanel" aria-labelledby="pills-bill-si-tab">
        <div class="table-responsive">
                <table style="table-layout: fixed; width: 100%;" class="table table-nowrap " id="bill-si">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID giao dịch</th>
                            <!-- <th scope="col">Ngân hàng</th>
                                    <th scope="col">Số tài khoản</th> -->
                            <th style="col">Nội dung</th>
                            <th scope="col">Ngày</th>
                            <th scope="col">Tổng tiền</th>
                            <th scope="col">Loại</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Transactions_Drop as $Transaction)
                        <tr>
                            <td>
                                {{$Transaction->transaction_id}}
                            </td>
                            <!-- <td><span class="badge bg-warning">{{$Transaction->bank}}</span></td>
                                    <td>{{$Transaction->account_number}}</td> -->
                            <td style="width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{$Transaction->description}}
                            </td>
                            <td>{{$Transaction->transaction_date}}</td>
                            <td>
                                @if ($Transaction->type === 'IN')
                                <span class="badge bg-secondary-subtle text-secondary badge-border">+{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                @elseif ($Transaction->type === 'OUT')
                                <span class="badge bg-danger-subtle text-danger badge-border">-{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                @else
                                <span class="badge bg-secondary-subtle text-secondary badge-border">{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                @endif
                            </td>

                            <td>
                                <span>
                                    @if ($Transaction->type === 'IN')
                                    <span class="badge rounded-pill border border-primary text-primary">Nạp số dư</span>
                                    @elseif ($Transaction->type === 'OUT')
                                    <span class="badge rounded-pill border border-danger text-danger">Chi số dư</span>
                                    @else
                                    <span>Unknown Type</span>
                                    @endif
                                </span>
                            </td>

                        </tr>

                        @endforeach

                    </tbody>
                </table>
                <script>
                    $(document).ready(function() {
                        $('#bill-si').DataTable({
                            "paging": true, // Bật phân trang
                            "searching": true, // Bật tìm kiếm
                            "ordering": true, // Bật sắp xếp
                            "info": true, // Hiển thị thông tin
                            "lengthMenu": [10, 20, 50, 100,200], // Số lượng dòng hiển thị

                            // Chỉnh Tiếng Việt
                            "language": {
                                "lengthMenu": "Hiển thị _MENU_giao dịch",
                                "zeroRecords": "Không tìm thấy dữ liệu",
                                "info": "Hiển thị _START_ đến _END_ của _TOTAL_ giao dịch",
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
        <!-- Nạp tiền -->
        <div class="tab-pane fade" id="pills-nap" role="tabpanel" aria-labelledby="pills-nap-tab">
        <div class="table-responsive">
                <table style="table-layout: fixed; width: 100%;" class="table table-nowrap " id="nap">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID giao dịch</th>
                            <!-- <th scope="col">Ngân hàng</th>
                                    <th scope="col">Số tài khoản</th> -->
                            <th style="col">Nội dung</th>
                            <th scope="col">Ngày</th>
                            <th scope="col">Tổng tiền</th>
                            <th scope="col">Loại</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Transaction_nap as $Transaction)
                        <tr>
                            <td>
                                {{$Transaction->transaction_id}}
                            </td>
                            <!-- <td><span class="badge bg-warning">{{$Transaction->bank}}</span></td>
                                    <td>{{$Transaction->account_number}}</td> -->
                            <td style="width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{$Transaction->description}}
                            </td>
                            <td>{{$Transaction->transaction_date}}</td>
                            <td>
                                @if ($Transaction->type === 'IN')
                                <span class="badge bg-secondary-subtle text-secondary badge-border">+{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                @elseif ($Transaction->type === 'OUT')
                                <span class="badge bg-danger-subtle text-danger badge-border">-{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                @else
                                <span class="badge bg-secondary-subtle text-secondary badge-border">{{ number_format($Transaction->amount, 0, '.', ',') }} VNĐ</span>
                                @endif
                            </td>

                            <td>
                                <span>
                                    @if ($Transaction->type === 'IN')
                                    <span class="badge rounded-pill border border-primary text-primary">Nạp số dư</span>
                                    @elseif ($Transaction->type === 'OUT')
                                    <span class="badge rounded-pill border border-danger text-danger">Chi số dư</span>
                                    @else
                                    <span>Unknown Type</span>
                                    @endif
                                </span>
                            </td>

                        </tr>

                        @endforeach

                    </tbody>
                </table>
                <script>
                    $(document).ready(function() {
                        $('#nap').DataTable({
                            "paging": true, // Bật phân trang
                            "searching": true, // Bật tìm kiếm
                            "ordering": true, // Bật sắp xếp
                            "info": true, // Hiển thị thông tin
                            "lengthMenu": [ 10, 20, 50, 100,200], // Số lượng dòng hiển thị

                            // Chỉnh Tiếng Việt
                            "language": {
                                "lengthMenu": "Hiển thị _MENU_giao dịch",
                                "zeroRecords": "Không tìm thấy dữ liệu",
                                "info": "Hiển thị _START_ đến _END_ của _TOTAL_ giao dịch",
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
        <!-- Chi tiêu ADS -->
        <div class="tab-pane fade" id="pills-ads" role="tabpanel" aria-labelledby="pills-ads-tab">
            <div class="table-responsive">
                <table style="table-layout: fixed; width: 100%;" class="table table-nowrap ">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID giao dịch</th>
                            <!-- <th scope="col">Ngân hàng</th>
                                    <th scope="col">Số tài khoản</th> -->
                            <th style="width: 350px;">Nội dung</th>
                            <th scope="col">Ngày</th>
                            <th scope="col">Tổng tiền</th>
                            <th scope="col">Loại</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Transactions_Drop as $Transaction)
                        <tr>
                            <td>
                                {{$Transaction->transaction_id}}
                            </td>
                            <!-- <td><span class="badge bg-warning">{{$Transaction->bank}}</span></td>
                                    <td>{{$Transaction->account_number}}</td> -->
                            <td style="width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{$Transaction->description}}
                            </td>


                            <td>{{$Transaction->transaction_date}}</td>
                            <td><span class="badge bg-info">{{ number_format($Transaction->amount, 0, '.', ',') }}VNĐ</span></td>

                            <td>
                                {{$Transaction->type}}
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
        <!-- Hoá đơn dịch vụ -->
        <div class="tab-pane fade" id="pills-dich-vu" role="tabpanel" aria-labelledby="pills-dich-vu-tab">
            <div class="table-responsive">
                <table style="table-layout: fixed; width: 100%;" class="table table-nowrap ">
                    <thead class="table-light">
                        <tr>
                            <th scope="col">ID giao dịch</th>
                            <!-- <th scope="col">Ngân hàng</th>
                                    <th scope="col">Số tài khoản</th> -->
                            <th style="width: 350px;">Nội dung</th>
                            <th scope="col">Ngày</th>
                            <th scope="col">Tổng tiền</th>
                            <th scope="col">Loại</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($Transactions as $Transaction)
                        <tr>
                            <td>
                                {{$Transaction->transaction_id}}
                            </td>
                            <!-- <td><span class="badge bg-warning">{{$Transaction->bank}}</span></td>
                                    <td>{{$Transaction->account_number}}</td> -->
                            <td style="width: 350px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{$Transaction->description}}
                            </td>


                            <td>{{$Transaction->transaction_date}}</td>
                            <td><span class="badge bg-info">{{ number_format($Transaction->amount, 0, '.', ',') }}VNĐ</span></td>

                            <td>
                                {{$Transaction->type}}
                            </td>
                        </tr>
                        @endforeach

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection