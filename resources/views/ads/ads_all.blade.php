@extends('layout')
@section('title', 'Danh sách Quảng Cáo')

@section('main')

<style>
    .table thead th {
        position: sticky;
        top: 0;
        background: #f8f9fa;
        z-index: 2;
    }
</style>

<div class="container-fluid" style="width: 100%; background: white;">
    <div class="row">
        <div class="col-lg-12">
            <div class="card" id="adsList">
                <div class="card-body pt-0">
                    <div>
                        <!-- Tabs hiển thị theo User -->
                        <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active All py-3" data-bs-toggle="tab" id="All" href="#home1" role="tab" aria-selected="true">
                                    <i class="ri-store-2-fill me-1 align-bottom"></i> Tất cả quảng cáo
                                </a>
                            </li>
                            @foreach($ads_all as $userName => $shops)
                            @foreach($shops as $shopName => $ads)
                            <li class="nav-item">
                                <a class="nav-link py-3" data-bs-toggle="tab" id="shop-{{ Str::slug($shopName) }}" href="#shop-{{ Str::slug($shopName) }}-content" role="tab" aria-selected="false">
                                    <i class="fas fa-store me-1"></i> {{ $shopName }}
                                </a>
                            </li>
                            @endforeach
                            @endforeach
                        </ul>

                        <!-- Nội dung Tabs -->
                        <div class="tab-content">
                            <!-- Tất cả quảng cáo -->
                            <div class="tab-pane fade show active" id="home1" role="tabpanel">
                                <div class="table-responsive table-card mb-1">
                                    <table class="table table-hover" id="ads_all-{{ Str::slug($shopName)}}">
                                        <thead class="text-muted table-light">
                                            <tr class="text-uppercase">
                                                <th>Mã Hóa Đơn</th>
                                                <th>Shop</th>
                                                <th>Ngày Chi</th>
                                                <th>Số Tiền</th>
                                                <th>VAT</th>
                                                <th>Tổng Cộng</th>
                                                <th>Thanh Toán</th>
                                                <th>Mã Thanh Toán</th>
                                                <th>Ngày Tạo</th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all text-black-50">
                                            @foreach($ads_all as $userName => $shops)
                                            @foreach($shops as $shopName => $ads)
                                            @foreach($ads as $ad)
                                            <tr>
                                                <td>{{ $ad['invoice_id'] }}</td>
                                                <td>{{ $shopName }}</td>
                                                <td>{{ $ad['date_range'] }}</td>
                                                <td>{{ number_format($ad['amount'], 0, ',', '.') }} đ</td>
                                                <td>{{ number_format($ad['vat'], 0, ',', '.') }} đ</td>
                                                <td>{{ number_format($ad['total_amount'] ?? 0, 0, ',', '.') }} đ</td>
                                                <td class="{{ $ad['payment_status'] == 'Chưa thanh toán' ? 'text-danger' : 'text-success' }}">
                                                    {{ $ad['payment_status'] }}
                                                </td>
                                                <td>{{ $ad['payment_code'] }}</td>
                                                <td>{{ $ad['created_at'] }}</td>
                                            </tr>
                                            @endforeach
                                            @endforeach
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <script>
                                        $(document).ready(function() {
                                            $('#ads_all-{{ Str::slug($shopName)}}').DataTable({
                                                "paging": true, // Bật phân trang
                                                "searching": true, // Bật tìm kiếm
                                                "ordering": true, // Bật sắp xếp
                                                "info": true, // Hiển thị thông tin
                                                "lengthMenu": [10, 20, 50, 100, 150], // Số lượng dòng hiển thị
                                                "order": [
                                                    [8, "desc"]
                                                ], // Mặc định sắp xếp cột thứ 3 (Ngày tạo đơn) theo mới nhất

                                                // Chỉnh Tiếng Việt
                                                "language": {
                                                    "lengthMenu": "Hiển thị _MENU_ quảng cáo",
                                                    "zeroRecords": "Không tìm thấy dữ liệu",
                                                    "info": "Hiển thị _START_ đến _END_ của _TOTAL_ quảng cáo",
                                                    "infoEmpty": "Không có dữ liệu để hiển thị",
                                                    "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                                                    "search": "",
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

                            <!-- Quảng cáo theo từng Shop -->
                            @foreach($ads_all as $userName => $shops)
                            @foreach($shops as $shopName => $ads)
                            <div class="tab-pane fade" id="shop-{{ Str::slug($shopName) }}-content" role="tabpanel">
                                <div class="table-responsive table-card mb-1">
                                    <table class="table table-nowrap align-middle table-hover" id="ads_shop_{{ Str::slug($shopName) }}_haha">
                                        <thead class="text-muted table-light">
                                            <tr class="text-uppercase">
                                                <th>Mã Hóa Đơn</th>
                                                <th>Ngày Chi</th>
                                                <th>Số Tiền</th>
                                                <th>VAT</th>
                                                <th>Tổng Cộng</th>
                                                <th>Thanh Toán</th>
                                                <th>Mã Thanh Toán</th>
                                                <th>Ngày tạo</th>
                                            </tr>
                                        </thead>
                                        <tbody class="text-black-50">
                                            @foreach($ads as $ad)
                                            <tr>
                                                <td>{{ $ad['invoice_id'] }}</td>
                                                <td>{{ $ad['date_range'] }}</td>
                                                <td>{{ number_format($ad['amount'], 0, ',', '.') }} đ</td>
                                                <td>{{ number_format($ad['vat'], 0, ',', '.') }} đ</td>
                                                <td>{{ number_format($ad['total_amount'] ?? 0, 0, ',', '.') }} đ</td>
                                                <td class="{{ $ad['payment_status'] == 'Chưa thanh toán' ? 'text-danger' : 'text-success' }}">
                                                    {{ $ad['payment_status'] }}
                                                </td>
                                                <td>{{ $ad['payment_code'] }}</td>
                                                <td>{{ $ad['created_at'] }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    <script>
                                        $(document).ready(function() {
                                            $('#ads_shop_{{ Str::slug($shopName) }}_haha').DataTable().destroy();
                                            $('#ads_shop_{{ Str::slug($shopName) }}_haha').DataTable({
                                                "paging": true, // Bật phân trang
                                                "searching": true, // Bật tìm kiếm
                                                "ordering": true, // Bật sắp xếp
                                                "info": true, // Hiển thị thông tin
                                                "lengthMenu": [10, 20, 50, 100, 150], // Số lượng dòng hiển thị
                                                "order": [
                                                    [7, "desc"]
                                                ], // Mặc định sắp xếp cột thứ 3 (Ngày tạo đơn) theo mới nhất

                                                // Chỉnh Tiếng Việt
                                                "language": {
                                                    "lengthMenu": "Hiển thị _MENU_ quảng cáo",
                                                    "zeroRecords": "Không tìm thấy dữ liệu",
                                                    "info": "Hiển thị _START_ đến _END_ của _TOTAL_ quảng cáo",
                                                    "infoEmpty": "Không có dữ liệu để hiển thị",
                                                    "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                                                    "search": "",
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
                            @endforeach
                        </div> <!-- End Tab Content -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection