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
                    

                    <!-- Tabs hiển thị theo Shop -->
                    <ul class="nav nav-tabs nav-tabs-custom nav-success mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active All py-3" data-bs-toggle="tab" id="All" href="#home1" role="tab" aria-selected="true">
                                <i class="ri-store-2-fill me-1 align-bottom"></i> Tất cả quảng cáo
                            </a>
                        </li>
                        @foreach($ads_shop as $shopName => $ads)
                            <li class="nav-item">
                                <a class="nav-link py-3" data-bs-toggle="tab" id="shop-{{ Str::slug($shopName) }}" href="#shop-{{ Str::slug($shopName) }}-content" role="tab" aria-selected="false">
                                    <i class="fas fa-store me-1"></i> {{ $shopName }}
                                </a>
                            </li>
                        @endforeach
                    </ul>

                    <!-- Nội dung Tabs -->
                    <div class="tab-content">
                        <!-- Tất cả quảng cáo -->
                        <div class="tab-pane fade show active" id="home1" role="tabpanel">
                            <div class="table-responsive table-card mb-1">
                                <table class="table table-hover">
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
                                            <th>Ngày tạo</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all text-black-50">
                                        @foreach($ads_shop as $shopName => $ads)
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
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Quảng cáo theo từng Shop -->
                        @foreach($ads_shop as $shopName => $ads)
                            <div class="tab-pane fade" id="shop-{{ Str::slug($shopName) }}-content" role="tabpanel">
                                <div class="table-responsive table-card mb-1">
                                    <table class="table table-nowrap align-middle table-hover">
                                        <thead class="text-muted table-light">
                                            <tr class="text-uppercase">
                                                <th>Mã Hóa Đơn</th>
                                                <th>Ngày Chi</th>
                                                <th>Số Tiền</th>
                                                <th>VAT</th>
                                                <th>Tổng Cộng</th>
                                                <th>Thanh Toán</th>
                                                <th>Mã Thanh Toán</th>
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
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endforeach
                    </div> <!-- End Tab Content -->
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
