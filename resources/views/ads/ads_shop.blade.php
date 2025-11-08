@extends('layout')
@section('title', 'Danh sách Quảng Cáo')

@section('main')

@include('ads.components.ads-styles')

{{-- Add CSS for no ads message --}}
<style>
    .no-ads-container {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
        text-align: center;
        background: white;
    }
    .no-ads-icon {
        font-size: 48px;
        color: #6c757d;
        margin-bottom: 20px;
    }
    .no-ads-message {
        font-size: 20px;
        color: #6c757d;
        margin-bottom: 10px;
    }
    .no-ads-submessage {
        font-size: 16px;
        color: #8c959d;
    }
</style>

<div class="container-fluid" style="width: 100%; background: white;">
    <div class="row">
        <div class="col-lg-12 p-0">
        <!-- <div class="d-flex flex-row gap-3 flex-wrap px-4 pt-4 pb-2" style="height: 450px;">
            <div class="card flex-fill" style="margin-bottom: 0px;">
                <div class="card-body p-0 d-flex align-items-center justify-content-center">
                    <x-chart-ads 
                        :labels="['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12']"
                        :datasets="[
                            [
                                'label' => 'Lovito',
                                'data' => [2500000, 8000000, 1500000, 4000000, 3200000, 9000000, 1000000, 3500000, 4200000, 6000000, 1800000, 7000000],
                                'borderColor' => '#FF0000', // đỏ
                                'backgroundColor' => 'rgba(255,0,0,0.18)',
                                'fill' => true,
                            ],
                            [
                                'label' => 'BRANIA',
                                'data' => [9000000, 2000000, 3500000, 7000000, 1000000, 9500000, 3000000, 8000000, 5000000, 4000000, 6000000, 7500000],
                                'borderColor' => '#FFD600', // vàng
                                'backgroundColor' => 'rgba(255,214,0,0.18)',
                                'fill' => true,
                            ],
                            [
                                'label' => 'DIVA HCM',
                                'data' => [5000000, 3000000, 1000000, 2000000, 8000000, 4000000, 6000000, 1500000, 9500000, 2500000, 7000000, 3500000],
                                'borderColor' => '#00C853', // xanh lá
                                'backgroundColor' => 'rgba(0,200,83,0.18)',
                                'fill' => true,
                            ]
                        ]"
                        title="Biểu đồ chi phí quảng cáo"
                        canvasId="adsChart1"
                    />
                </div>
            </div>
            <div class="card flex-fill" style="max-width: 400px; min-width: 400px; margin-bottom: 0px;">
                <div class="card-body d-flex align-items-center justify-content-center">
                    <x-chart-ads-poler-area
                        :labels="['Lovito', 'BRANIA', 'DIVA HCM']"
                        dataset-label="Chi tiêu quảng cáo"
                        :data="[1200000, 900000, 1500000]"
                        :background-colors="['rgba(255, 99, 132, 0.5)', 'rgba(54, 162, 235, 0.5)', 'rgba(255, 206, 86, 0.5)']"
                        title="Biểu đồ chi tiêu quảng cáo"
                        canvas-id="adsPolarAreaChart"
                    />
                </div>
            </div>
        </div> -->
                        
                     <div>
                    <!-- Tabs hiển thị theo Shop -->
                    <ul class="nav nav-tabs nav-tabs-custom nav-success mb-0 d-none d-md-flex" role="tablist">
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
                            {{-- Desktop Filter --}}
                            <div class="d-none d-md-block">
                                @include('ads.components.ads-filters')
                            </div>
                            {{-- Mobile Filter --}}
                            <div class="d-block d-md-none">
                                @include('ads.components.ads-mobile-filters')
                            </div>
                            @php
                                $allAds = collect();
                                foreach($ads_shop as $shopName => $ads) {
                                    foreach($ads as $ad) {
                                        $ad['shop_name'] = $shopName;
                                        $allAds->push($ad);
                                    }
                                }
                                $allAds = $allAds->sortByDesc('created_at')->values();
                            @endphp
                            @if($allAds->isEmpty())
                                <div class="no-ads-container">
                                    <div class="no-ads-icon">
                                        <i class="ri-search-line"></i>
                                    </div>
                                    <div class="no-ads-message">Không có hóa đơn quảng cáo nào</div>
                                    <div class="no-ads-submessage">Tài khoản này hiện chưa có quảng cáo.</div>
                                </div>
                            @else
                                @include('ads.components.ads-table', ['ads' => $allAds, 'shopName' => null])
                            @endif
                        </div>

                        <!-- Quảng cáo theo từng Shop -->
                        @foreach($ads_shop as $shopName => $ads)
                        <div class="tab-pane fade" id="shop-{{ Str::slug($shopName)}}-content" role="tabpanel">
                            {{-- Desktop Filter --}}
                            <div class="d-none d-md-block">
                                @include('ads.components.ads-filters')
                            </div>
                            {{-- Mobile Filter --}}
                            <div class="d-block d-md-none">
                                @include('ads.components.ads-mobile-filters')
                            </div>
                            @if(empty($ads))
                                <div class="no-ads-container">
                                    <div class="no-ads-icon">
                                        <i class="ri-search-line"></i>
                                    </div>
                                    <div class="no-ads-message">Không có hóa đơn quảng cáo nào</div>
                                    <div class="no-ads-submessage">Tài khoản này hiện chưa có quảng cáo.</div>
                                </div>
                            @else
                                @include('ads.components.ads-table', ['ads' => $ads, 'shopName' => $shopName])
                            @endif
                        </div>
                        @endforeach
                    </div> <!-- End Tab Content -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize copy functionality
        $(document).on('click', '[data-clipboard]', function(e) {
            e.stopPropagation();
            const text = $(this).data('clipboard');
            navigator.clipboard.writeText(text).then(() => {
                $(this).removeClass('ri-clipboard-line').addClass('ri-check-line text-success');
                setTimeout(() => {
                    $(this).removeClass('ri-check-line text-success').addClass('ri-clipboard-line');
                }, 1500);
            });
        });

        // Initialize DataTable for all ads
        $('#adsTable').DataTable({
            "paging": true,
            "searching": false, // Disable built-in search since we have custom search
            "ordering": true,
            "info": true,
            "lengthMenu": [10, 20, 50, 100, 150],
            "order": [[8, "desc"]], // Sort by created_at column
            "dom": '<""<"col-sm-12"tr>>' +
                   '<"row justify-content-between align-items-center mt-2 mx-0 no-gutters"<"col-auto"l><"col-auto"i><"col-auto"p>>',
            "language": {
                "lengthMenu": "Hiển thị _MENU_ quảng cáo",
                "zeroRecords": "Không tìm thấy dữ liệu",
                "info": "",
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

        // Di chuyển phân trang ra ngoài table cho mobile
        function movePaginationForMobile() {
            if (window.innerWidth < 768) {
                // Di chuyển phân trang ra ngoài table cho mobile
                $('#mobile-pagination').html($('.dataTables_paginate'));
                $('#mobile-pagination').append($('.dataTables_info'));
            } else {
                // Đưa lại vào vị trí cũ cho desktop nếu cần
                $('.dataTables_wrapper .row.justify-content-between .col-auto:last').append($('.dataTables_paginate'));
                $('.dataTables_wrapper .row.justify-content-between .col-auto').first().append($('.dataTables_info'));
            }
        }
        movePaginationForMobile();
        $(window).on('resize', movePaginationForMobile);
    });
</script>

@endsection