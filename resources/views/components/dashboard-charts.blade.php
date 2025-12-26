

<!-- Chart Section -->
<div class="row" id="chartContainer">
    <!-- Line Chart -->
    <div class="col-lg-8 col-12 mb-3">
        <div class="bg-white rounded-3 h-100">
            <div class="card-header align-items-center d-flex p-3">
                <h4 class="card-title mb-0 flex-grow-1 fs-14 md:fs-16">Biểu đồ Tổng giá vốn & Phí Drop</h4>
                <div class="flex-shrink-0">
                    <div class="dropdown card-header-dropdown">
                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="fw-semibold text-uppercase fs-12">Thời gian:</span>
                            <span class="text-muted" id="chartDateRange">
                                Tháng này <i class="mdi mdi-chevron-down ms-1"></i>
                            </span>
                        </a>
                        <div class="dropdown-menu dropdown-menu-end">
                            <a class="dropdown-item" href="#" onclick="initChart('last7days')">7 ngày trước</a>
                            <a class="dropdown-item" href="#" onclick="initChart('lastmonth')">Tháng trước</a>
                            <a class="dropdown-item" href="#" onclick="initChart('thismonth')">Tháng này</a>
                            <a class="dropdown-item" href="#" onclick="initChart('last12months')">12 tháng qua</a>
                            <a class="dropdown-item" href="#" onclick="initChart('thisyear')">Năm nay</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="p-3">
                <canvas id="revenueChart" style="max-height: 280px; width: 100%;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Pie Chart -->
    <div class="col-lg-4 col-12 mb-3">
        <div class="bg-white rounded-3 h-100">
            <div class="card-header align-items-center d-flex p-3">
                <h4 class="card-title mb-0 flex-grow-1 fs-14 md:fs-16">Thống kê chi phí & đơn hàng</h4>
            </div>
            <div class="p-3 d-flex align-items-center justify-content-center">
                <canvas id="pieChart" style="max-height: 280px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let revenueChart = null;
    let pieChart = null;
    let currentRange = 'thismonth';

    // Hàm khởi tạo biểu đồ
    function initChart(range = 'thismonth') {
        currentRange = range;
        const ctx = document.getElementById('revenueChart');
        const pieCtx = document.getElementById('pieChart');
        
        if (!ctx || !pieCtx) return;

        // Hủy biểu đồ cũ nếu tồn tại
        if (revenueChart) {
            revenueChart.destroy();
        }
        if (pieChart) {
            pieChart.destroy();
        }

        // Khởi tạo biểu đồ doanh thu
        revenueChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: [],
                datasets: [
                    {
                        label: 'Tổng giá vốn',
                        data: [],
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.5)',
                        tension: 0.4
                    },
                    {
                        label: 'Phí Drop',
                        data: [],
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.5)',
                        tension: 0.4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.dataset.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed.y !== null) {
                                    label += new Intl.NumberFormat('vi-VN', {
                                        style: 'currency',
                                        currency: 'VND'
                                    }).format(context.parsed.y);
                                }
                                return label;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('vi-VN', {
                                    style: 'currency',
                                    currency: 'VND',
                                    maximumFractionDigits: 0
                                }).format(value);
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        }
                    }
                }
            }
        });

        // Khởi tạo biểu đồ pie
        pieChart = new Chart(pieCtx, {
            type: 'pie',
            data: {
                labels: ['Tổng đơn hàng', 'Tổng sản phẩm bán ra', 'Tổng giá vốn', 'Tổng phí Drop', 'Tổng chi phí ADS'],
                datasets: [{
                    data: [0, 0, 0, 0, 0],
                    backgroundColor: [
                        'rgb(255, 99, 132)',
                        'rgb(54, 162, 235)',
                        'rgb(75, 192, 192)',
                        'rgb(255, 159, 64)',
                        'rgb(153, 102, 255)'
                    ],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            padding: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                
                                switch(label) {
                                    case 'Tổng đơn hàng':
                                        return `${label}: ${new Intl.NumberFormat('vi-VN').format(value)} đơn`;
                                    case 'Tổng sản phẩm bán ra':
                                        return `${label}: ${new Intl.NumberFormat('vi-VN').format(value)} sản phẩm`;
                                    case 'Tổng giá vốn':
                                    case 'Tổng phí Drop':
                                    case 'Tổng chi phí ADS':
                                        return `${label}: ${new Intl.NumberFormat('vi-VN', {
                                            style: 'currency',
                                            currency: 'VND'
                                        }).format(value)}`;
                                }
                            }
                        }
                    }
                }
            }
        });

        updateChartData(range);
    }

    function updateChartData(range) {
        // Cập nhật text hiển thị
        const rangeText = {
            'last7days': '7 ngày trước',
            'lastmonth': 'Tháng trước',
            'thismonth': 'Tháng này',
            'last12months': '12 tháng qua',
            'thisyear': 'Năm nay'
        };
        document.getElementById('chartDateRange').innerHTML = `${rangeText[range]} <i class="mdi mdi-chevron-down ms-1"></i>`;

        // Gọi API cho biểu đồ đường
        fetch(`/api/chart?range=${range}`)
            .then(response => response.json())
            .then(data => {
                if (!revenueChart) {
                    console.warn('Revenue chart not initialized');
                    return;
                }

                // Format ngày tháng
                const labels = data.map(item => {
                    const date = new Date(item.date);
                    return date.toLocaleDateString('vi-VN', {
                        day: '2-digit',
                        month: '2-digit'
                    });
                });

                // Cập nhật biểu đồ doanh thu
                revenueChart.data.labels = labels;
                revenueChart.data.datasets[0].data = data.map(item => item.total_expense);
                revenueChart.data.datasets[1].data = data.map(item => item.dropship_fee);
                revenueChart.update();
            })
            .catch(error => {
                console.error('Error fetching revenue chart data:', error);
            });

        // Gọi API cho biểu đồ tròn
        fetch(`/api/chart-v1?range=${range}`)
            .then(response => response.json())
            .then(data => {
                if (!pieChart) {
                    console.warn('Pie chart not initialized');
                    return;
                }

                // Lưu dữ liệu vào biến global để các card có thể sử dụng
                window.dashboardData = data;

                // Cập nhật biểu đồ pie với dữ liệu mới
                pieChart.data.datasets[0].data = [
                    data.total_orders,
                    parseInt(data.total_quantity_sold),
                    parseFloat(data.total_bill_paid),
                    parseFloat(data.total_dropship),
                    parseFloat(data.total_ads)
                ];
                pieChart.update();

                // Trigger event để thông báo dữ liệu đã được cập nhật
                window.dispatchEvent(new CustomEvent('dashboardDataUpdated', { detail: data }));
            })
            .catch(error => {
                console.error('Error fetching pie chart data:', error);
            });
    }

    // Khởi tạo biểu đồ khi component được load
    function initializeCharts() {
        if (document.getElementById('chartContainer')) {
            initChart(currentRange);
        }
    }

    // Khởi tạo ban đầu và xử lý navigation
    document.addEventListener('DOMContentLoaded', initializeCharts);

    // Xử lý khi URL thay đổi (SPA navigation)
    let lastUrl = location.href;
    new MutationObserver(() => {
        const url = location.href;
        if (url !== lastUrl) {
            lastUrl = url;
            setTimeout(initializeCharts, 100); // Thêm delay nhỏ để đảm bảo DOM đã được cập nhật
        }
    }).observe(document, {subtree: true, childList: true});

    // Xử lý navigation events
    ['turbolinks:load', 'page:load', 'load', 'popstate'].forEach(event => {
        window.addEventListener(event, () => {
            setTimeout(initializeCharts, 100);
        });
    });
</script>
@endpush 