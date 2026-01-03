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

        // ============================================
        // KHỞI TẠO DATATABLE CHO BẢNG QUẢNG CÁO
        // Bảng: #adsTable (trong file ads-table.blade.php)
        // ============================================
        
        // Kiểm tra xem bảng có tồn tại và chưa được khởi tạo chưa
        if ($('#adsTable').length && !$.fn.DataTable.isDataTable('#adsTable')) {
            $('#adsTable').DataTable({
                // 1. BẬT PHÂN TRANG
                "paging": true,
                
                // 2. BẬT TÌM KIẾM (THAY ĐỔI TỪ false THÀNH true)
                "searching": true, // ✅ ĐÃ BẬT TÌM KIẾM
                
                // 3. BẬT SẮP XẾP
                "ordering": true,
                
                // 4. BẬT HIỂN THỊ THÔNG TIN
                "info": true,
                
                // 5. TÙY CHỌN SỐ MỤC MỖI TRANG
                "lengthMenu": [[10, 20, 50, 100, 150], [10, 20, 50, 100, 150]],
                "pageLength": 10, // Số mục mặc định mỗi trang
                
                // 6. SẮP XẾP MẶC ĐỊNH (theo cột Ngày tạo - cột thứ 8, giảm dần)
                "order": [[8, "desc"]],
                
                // 7. CẤU HÌNH DOM (Layout) - QUAN TRỌNG: THÊM 'f' ĐỂ HIỂN THỊ Ô TÌM KIẾM
                // 'l' = lengthMenu (dropdown chọn số mục/trang)
                // 'f' = search box (Ô TÌM KIẾM) ✅
                // 't' = table (bảng)
                // 'i' = info (thông tin)
                // 'p' = pagination (phân trang)
                "dom": '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                       '<"row"<"col-sm-12"tr>>' +
                       '<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                
                // 8. NGÔN NGỮ TIẾNG VIỆT
                "language": {
                    "lengthMenu": "Hiển thị _MENU_ quảng cáo",
                    "zeroRecords": "Không tìm thấy dữ liệu",
                    "info": "Hiển thị _START_ đến _END_ trong tổng số _TOTAL_ quảng cáo", // ✅ HIỂN THỊ THÔNG TIN ĐẦY ĐỦ
                    "infoEmpty": "Không có dữ liệu để hiển thị",
                    "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                    "search": "Tìm kiếm:", // ✅ LABEL CHO Ô TÌM KIẾM
                    "searchPlaceholder": "Nhập từ khóa...", // ✅ PLACEHOLDER CHO Ô TÌM KIẾM
                    "paginate": {
                        "first": "Trang đầu",
                        "last": "Trang cuối",
                        "next": "Tiếp theo",
                        "previous": "Quay lại"
                    }
                },
                
                // 9. RESPONSIVE (Tự động điều chỉnh trên mobile)
                "responsive": true,
                
                // 10. KHÔNG HIỂN THỊ LOADING
                "processing": false
            });
        }

        // Di chuyển phân trang ra ngoài table cho mobile
        function movePaginationForMobile() {
            if (window.innerWidth < 768) {
                // Di chuyển phân trang ra ngoài table cho mobile
                const paginate = $('.dataTables_paginate');
                const info = $('.dataTables_info');
                if (paginate.length && $('#mobile-pagination').length) {
                    $('#mobile-pagination').html('').append(paginate).append(info);
                }
            }
        }
        
        // Wait for DataTable to initialize then move pagination
        setTimeout(function() {
            movePaginationForMobile();
            // Đảm bảo ô tìm kiếm hiển thị
            if ($('.dataTables_filter').length) {
                $('.dataTables_filter').show();
            }
        }, 100);
        
        $(window).on('resize', function() {
            setTimeout(movePaginationForMobile, 100);
        });
        
        // Reinitialize pagination when tab changes
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
            setTimeout(function() {
                if ($.fn.DataTable.isDataTable('#adsTable')) {
                    $('#adsTable').DataTable().draw();
                    movePaginationForMobile();
                    // Đảm bảo ô tìm kiếm hiển thị khi chuyển tab
                    if ($('.dataTables_filter').length) {
                        $('.dataTables_filter').show();
                    }
                }
            }, 100);
        });
    });
</script>

@endsection