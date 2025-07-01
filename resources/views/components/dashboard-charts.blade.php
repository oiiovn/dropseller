<style>
    /* Chart buttons responsive */
    .btn-group .btn {
        font-size: 10px !important;
        padding: 0.2rem 0.4rem !important;
    }

    .card-header .btn-group {
        flex-wrap: wrap;
        gap: 2px;
    }

    .card-title {
        font-size: 12px !important;
    }

    .card-header {
        flex-direction: column;
        gap: 0.5rem;
    }

    /* Chart header responsive */
    .card-header.align-items-center.d-flex {
        flex-direction: column !important;
        align-items: flex-start !important;
    }

    .card-header .flex-shrink-0 {
        width: 100%;
        margin-top: 0.5rem;
    }

    .dropdown-menu {
        font-size: 12px !important;
    }

    /* Chart time filter responsive */
    .card-header-dropdown .dropdown-btn {
        font-size: 10px !important;
    }

    .card-header-dropdown .fs-12 {
        font-size: 10px !important;
    }

    .chart-time-filter {
        font-size: 12px !important;
        font-weight: 600;
        padding: 0.25rem 0.25rem !important;
    }

    /* Pie chart responsive */
    #pieChart {
        max-height: 250px !important;
    }

    /* Đảm bảo charts có cùng chiều cao */
    .chart-container {
        height: 300px;
    }

    @media (min-width: 992px) {
        .card-title {
            font-size: 14px !important;
        }
        
        .card-header {
            flex-direction: row;
            justify-content: space-between;
            align-items: center;
        }

        /* Reset chart header for desktop */
        .card-header.align-items-center.d-flex {
            flex-direction: row !important;
            align-items: center !important;
        }

        .card-header .flex-shrink-0 {
            width: auto;
            margin-top: 0;
        }

        .dropdown-menu {
            font-size: 14px !important;
        }

        /* Chart time filter desktop */
        .card-header-dropdown .dropdown-btn {
            font-size: 12px !important;
        }

        .card-header-dropdown .fs-12 {
            font-size: 12px !important;
        }

        .chart-time-filter {
            font-size: 14px !important;
            font-weight: 600;
            padding: 0.25rem 0.25rem !important;
        }
    }

    /* Time filter styling */
    .chart-time-filter {
        transition: all 0.2s ease;
        cursor: pointer;
    }
    
    .chart-time-filter:hover {
        background-color: #f8f9fa !important;
        color: #495057 !important;
    }
    
    .chart-time-filter:active,
    .chart-time-filter.active {
        background-color: #007bff !important;
        color: white !important;
    }
    
    .card-header-dropdown .dropdown-btn {
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .card-header-dropdown .dropdown-btn:hover {
        color: #007bff !important;
    }
    
    /* Custom date picker button styling */
    .custom-date-picker-btn {
        background: linear-gradient(45deg, #007bff, #0056b3);
        border: none;
        color: white;
        padding: 0.375rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(0,123,255,0.25);
    }
    
    .custom-date-picker-btn:hover {
        background: linear-gradient(45deg, #0056b3, #004085);
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(0,123,255,0.35);
        color: white;
    }
    
    .custom-date-picker-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(0,123,255,0.25);
    }
    
    /* Date Range Picker Styles */
    .date-range-picker {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
        padding: 0.5rem;
    }
    
    .date-range-picker .row {
        margin: 0;
        display: flex;
        flex-wrap: wrap;
    }
    
    .date-range-picker .col-md-6 {
        flex: 1;
        max-width: none;
        padding: 0.25rem;
    }
    
    #customDateModal .modal-header {
        padding: 0.75rem 1rem;
        border-bottom: 1px solid #dee2e6;
    }
    
    #customDateModal .modal-body {
        padding: 1rem;
    }
    
    #customDateModal .modal-footer {
        padding: 0.75rem 1rem;
        border-top: 1px solid #dee2e6;
    }
    
    #customDateModal .modal-title {
        font-size: 1rem;
        font-weight: 600;
    }
    
    #customDateModal hr {
        margin: 0.5rem 0;
        opacity: 0.3;
    }
    
    #customDateModal .text-muted {
        font-size: 0.7rem;
        margin-top: 0.5rem !important;
    }
    
    /* Tablet responsive - keep horizontal layout */
    @media (min-width: 577px) and (max-width: 768px) {
        .month-calendar {
            min-width: 200px;
            padding: 0.4rem;
        }
        
        .custom-day {
            width: 1.6rem;
            height: 1.6rem;
            font-size: 0.68rem;
        }
    }
    
    /* Mobile responsive for date picker */
    @media (max-width: 576px) {
        .date-range-picker .row {
            flex-direction: column;
            gap: 0.3rem;
        }
        
        .custom-day {
            width: 1.5rem;
            height: 1.5rem;
            font-size: 0.65rem;
        }
        
        .calendar-title {
            font-size: 0.75rem;
        }
        
        .calendar-day-header {
            font-size: 0.55rem;
            padding: 0.125rem;
        }
        
        .month-calendar {
            padding: 0.375rem;
            min-width: 180px;
        }
        
        .selected-range {
            padding: 0.375rem;
        }
        
        .selected-range strong,
        .selected-range span {
            font-size: 0.65rem;
        }
    }
    
    .month-calendar {
        padding: 0.5rem;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        background: #fff;
        width: 100%;
        min-width: 220px;
    }
    
    .calendar-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
        padding: 0.25rem 0;
    }
    
    .calendar-nav-btn {
        background: none;
        border: none;
        padding: 0.125rem 0.25rem;
        border-radius: 0.125rem;
        cursor: pointer;
        color: #6c757d;
        transition: all 0.2s ease;
        font-size: 0.75rem;
    }
    
    .calendar-nav-btn:hover {
        background-color: #f8f9fa;
        color: #495057;
    }
    
    .calendar-title {
        font-weight: 600;
        font-size: 0.8rem;
        color: #495057;
    }
    
    .calendar-grid {
        display: grid;
        grid-template-columns: repeat(7, 1fr);
       
    }
    
    .calendar-day-header {
        text-align: center;
        font-weight: 600;
        font-size: 0.6rem;
        color: #6c757d;
        padding: 0.25rem 0.125rem;
        text-transform: uppercase;
    }
    
    .custom-day {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 2rem;
        border-radius: 0.125rem;
        cursor: pointer;
        font-size: 0.7rem;
        transition: all 0.2s ease;
        position: relative;
        margin: 0.5px;
    }
    
    .custom-day:hover {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    
    .custom-day.focused {
        background-color: #007bff;
        color: white;
        font-weight: 600;
    }
    
    .custom-day.range {
        background-color: #007bff;
        color: white;
    }
    
    .custom-day.faded {
        background-color: #bbdefb;
        color: #1976d2;
    }
    
    .custom-day.outside-month {
        color: #adb5bd;
        cursor: default;
    }
    
    .custom-day.outside-month:hover {
        background-color: transparent;
        color: #adb5bd;
    }
    
    .custom-day.start-range {
        background-color: #007bff;
        color: white;
        border-top-left-radius: 0.25rem;
        border-bottom-left-radius: 0.25rem;
    }
    
    .custom-day.end-range {
        background-color: #007bff;
        color: white;
        border-top-right-radius: 0.25rem;
        border-bottom-right-radius: 0.25rem;
    }
    
    .custom-day.in-range {
        background-color: #e3f2fd;
        color: #1976d2;
        border-radius: 0;
    }
    
    .selected-range {
        background-color: #f8f9fa;
        padding: 0.5rem;
        border-radius: 0.25rem;
        border: 1px solid #dee2e6;
        margin: 0.5rem 0;
    }
    
    .selected-range strong {
        color: #495057;
        font-size: 0.75rem;
    }
    
    .selected-range span {
        color: #007bff;
        font-family: monospace;
        font-size: 0.7rem;
    }
    
    .selected-range-inline {
        background-color: #f8f9fa;
        padding: 0.5rem;
        border-radius: 0.25rem;
        border: 1px solid #dee2e6;
        max-height: 280px;
        overflow-y: auto;
    }
    
    .selected-range-inline strong {
        color: #495057;
        font-size: 0.75rem;
    }
    
    .selected-range-inline span {
        color: #007bff;
        font-family: monospace;
        font-size: 0.7rem;
        margin-left: 0.25rem;
    }
    
    .selected-range-inline .chart-time-filter {
        display: block;
        padding: 0.375rem 0.75rem;
        color: #212529;
        text-decoration: none;
        font-size: 0.875rem;
        border-radius: 0.25rem;
        margin-bottom: 0.25rem;
        transition: all 0.2s ease;
    }
    
    .selected-range-inline .chart-time-filter:hover {
        background-color: #f8f9fa;
        color: #495057;
    }
    
    .selected-range-inline .chart-time-filter:active {
        background-color: #007bff;
        color: white;
    }
    
    .selected-date-display {
        background-color: #f1f3f4;
        padding: 0.5rem;
        border-radius: 0.25rem;
        border: 1px solid #dee2e6;
        font-size: 0.875rem;
    }
    
    .selected-date-display strong {
        color: #495057;
    }
    
    .selected-date-display span {
        color: #007bff;
        font-family: monospace;
        margin-left: 0.25rem;
    }
</style>

<!-- Chart Section -->
<div class="row">
    <!-- Line Chart -->
    <div class="col-lg-8 col-12 mb-3">
        <div class="bg-white rounded-3 h-100">
            <div class="card-header align-items-center d-flex p-3">
                <h4 class="card-title mb-0 flex-grow-1 fs-14 md:fs-16">Biểu đồ Tổng giá vốn & Phí Drop</h4>
                <div class="flex-shrink-0">
                    <div class="dropdown card-header-dropdown">
                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="fw-semibold text-uppercase fs-12">Thời gian:</span>
                            <span class="text-muted" id="customLineChartDate">
                                7 ngày trước <i class="mdi mdi-chevron-down ms-1"></i>
                            </span>
                        </a>
                        
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
                <h4 class="card-title mb-0 flex-grow-1 fs-14 md:fs-16">Thống kê Đơn hàng & Sản phẩm</h4>
                <div class="flex-shrink-0">
                    <div class="dropdown card-header-dropdown">
                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span class="fw-semibold text-uppercase fs-12">Thời gian:</span>
                            <span class="text-muted" id="customPieChartDate">
                                7 ngày trước <i class="mdi mdi-chevron-down ms-1"></i>
                            </span>
                        </a>
                    </div>
                </div>
            </div>
            <div class="p-3 d-flex align-items-center justify-content-center">
                <canvas id="pieChart" style="max-height: 280px; max-width: 100%;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // Chart configuration
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        
        // ========== LEGEND ICONS CONFIG ==========
        // Bạn có thể thay đổi icons tại đây:
        const LEGEND_ICONS = {
            totalBill: '',    // Icon cho "Tổng giá vốn" - có thể thay: 💵 💎 🪙 💳 
            feeDrop: '',      // Icon cho "Phí Drop" - có thể thay: 📦 🚛 ⚡ 🔄
            orders: '',       // Icon cho "Đơn hàng" - có thể thay: 🛒 📋 📝 🎯
            products: ''     // Icon cho "Sản phẩm" - có thể thay: 📱 💻 🎁 🏷️
        };
        
        // Helper functions
        const CHART_COLORS = {
            red: 'rgb(255, 99, 132)',
            orange: 'rgb(255, 159, 64)',
            yellow: 'rgb(255, 205, 86)',
            green: 'rgb(75, 192, 192)',
            blue: 'rgb(54, 162, 235)',
            purple: 'rgb(153, 102, 255)',
            grey: 'rgb(201, 203, 207)'
        };

        function transparentize(color, opacity) {
            const alpha = opacity === undefined ? 0.5 : 1 - opacity;
            return color.replace('rgb', 'rgba').replace(')', `, ${alpha})`);
        }

        function getRandomNumber(min, max) {
            return Math.floor(Math.random() * (max - min + 1)) + min;
        }

        function generateRandomData(count, min, max) {
            const data = [];
            for (let i = 0; i < count; i++) {
                data.push(getRandomNumber(min, max));
            }
            return data;
        }

        // Custom tooltip positioner
        Chart.Tooltip.positioners.bottom = function(items) {
            const pos = Chart.Tooltip.positioners.average(items);
            
            if (pos === false) {
                return false;
            }
            
            const chart = this.chart;
            
            return {
                x: pos.x,
                y: chart.chartArea.bottom,
                xAlign: 'center',
                yAlign: 'bottom',
            };
        };

                    // Function để tạo labels 7 ngày gần nhất
            function getLast7DaysLabels() {
                const labels = [];
                const today = new Date();
                
                for (let i = 6; i >= 0; i--) {
                    const date = new Date(today);
                    date.setDate(today.getDate() - i);
                    const dayMonth = date.getDate().toString().padStart(2, '0') + '/' + 
                                   (date.getMonth() + 1).toString().padStart(2, '0');
                    labels.push(dayMonth);
                }
                
                return labels;
            }

            // Data với 7 ngày gần nhất
            const monthLabels = getLast7DaysLabels();
            const totalBillData = [120, 190, 300, 500, 200, 300, 450]; // Tổng giá vốn - 7 ngày
            const totalDropshipData = [20, 35, 45, 80, 40, 60, 75]; // Phí Drop - 7 ngày
        
        const data = {
            labels: monthLabels,
            datasets: [
                {
                    label: 'Tổng giá vốn',
                    data: totalBillData,
                    fill: false,
                    borderColor: CHART_COLORS.red,
                    backgroundColor: transparentize(CHART_COLORS.red),
                    tension: 0.4
                },
                {
                    label: 'Phí Drop', 
                    data: totalDropshipData,
                    fill: false,
                    borderColor: CHART_COLORS.blue,
                    backgroundColor: transparentize(CHART_COLORS.blue),
                    tension: 0.4
                }
            ]
        };

        // Chart configuration
        const config = {
            type: 'line',
            data: data,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Biểu đồ Tổng giá vốn & Phí Drop'
                    },
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 12,
                                weight: '500'
                            },
                            padding: 15,
                            generateLabels: function(chart) {
                                const original = Chart.defaults.plugins.legend.labels.generateLabels;
                                const labels = original.call(this, chart);
                                
                                labels.forEach((label, index) => {
                                    if (index === 0) {
                                        label.text = LEGEND_ICONS.totalBill + ' ' + label.text;  // Icon tiền cho "Tổng giá vốn"
                                    } else if (index === 1) {
                                        label.text = LEGEND_ICONS.feeDrop + ' ' + label.text;  // Icon vận chuyển cho "Phí Drop"
                                    }
                                });
                                
                                return labels;
                            }
                        }
                    },
                    tooltip: {
                        position: 'average'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: false
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        };

        // Create chart
        const revenueChart = new Chart(ctx, config);

        // Action buttons functionality (optional)
        function randomizeData() {
            revenueChart.data.datasets.forEach((dataset, index) => {
                if (index === 0) {
                    // Tổng giá vốn: từ 0-1000 nghìn VNĐ
                    dataset.data = generateRandomData(revenueChart.data.labels.length, 0, 1000);
                } else {
                    // Phí Drop: từ 0-200 nghìn VNĐ
                    dataset.data = generateRandomData(revenueChart.data.labels.length, 0, 200);
                }
            });
            revenueChart.update();
        }

        function addDataset() {
            const data = revenueChart.data;
            const colors = Object.values(CHART_COLORS);
            const colorIndex = data.datasets.length % colors.length;
            const dsColor = colors[colorIndex];
            
            const datasetLabels = ['Doanh thu bán hàng', 'Chi phí vận chuyển', 'Lợi nhuận ròng', 'Chi phí quảng cáo'];
            const labelIndex = (data.datasets.length - 2) % datasetLabels.length;
            
            const newDataset = {
                label: datasetLabels[labelIndex] + ' (nghìn VNĐ)',
                backgroundColor: transparentize(dsColor, 0.5),
                borderColor: dsColor,
                data: generateRandomData(data.labels.length, 0, 300), // Dữ liệu từ 0-300 nghìn VNĐ
            };
            revenueChart.data.datasets.push(newDataset);
            revenueChart.update();
        }

        function addData() {
            const data = revenueChart.data;
            if (data.datasets.length > 0) {
                // Thêm một ngày mới (giả lập)
                const today = new Date();
                const newDate = new Date(today.getTime() + (data.labels.length - 6) * 24 * 60 * 60 * 1000);
                const dateStr = newDate.getDate().toString().padStart(2, '0') + '/' + 
                               (newDate.getMonth() + 1).toString().padStart(2, '0');
                data.labels.push(dateStr);

                // Thêm dữ liệu ngẫu nhiên cho các dataset
                for (let index = 0; index < data.datasets.length; ++index) {
                    data.datasets[index].data.push(getRandomNumber(0, 500)); // Giá trị từ 0-500 nghìn VNĐ
                }

                revenueChart.update();
            }
        }

        function removeDataset() {
            if (revenueChart.data.datasets.length > 1) {
                revenueChart.data.datasets.pop();
                revenueChart.update();
            }
        }

        function removeData() {
            if (revenueChart.data.labels.length > 1) {
                revenueChart.data.labels.splice(-1, 1); // remove the label first

                revenueChart.data.datasets.forEach(dataset => {
                    dataset.data.pop();
                });

                revenueChart.update();
            }
        }

        // Export functions to window object for button access
        window.chartActions = {
            randomizeData: randomizeData,
            addDataset: addDataset,
            addData: addData,
            removeDataset: removeDataset,
            removeData: removeData
        };

        // ==================== PIE CHART ====================
        const pieCtx = document.getElementById('pieChart').getContext('2d');
        
        // Fake data cho pie chart
        const pieData = {
            labels: ['Đơn hàng', 'Sản phẩm bán ra'],
            datasets: [{
                label: 'Thống kê 7 ngày',
                data: [85, 320], // 85 đơn hàng, 320 sản phẩm bán ra
                backgroundColor: [
                    CHART_COLORS.red,
                    CHART_COLORS.blue,
                    CHART_COLORS.green,
                    CHART_COLORS.orange,
                    CHART_COLORS.purple
                ],
                borderWidth: 2,
                borderColor: '#fff'
            }]
        };

        const pieConfig = {
            type: 'pie',
            data: pieData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 11,
                                weight: '500'
                            },
                            padding: 12,
                            generateLabels: function(chart) {
                                const original = Chart.defaults.plugins.legend.labels.generateLabels;
                                const labels = original.call(this, chart);
                                
                                labels.forEach((label, index) => {
                                    if (label.text === 'Đơn hàng') {
                                        label.text = LEGEND_ICONS.orders + ' ' + label.text;  // Icon đơn hàng
                                    } else if (label.text === 'Sản phẩm bán ra') {
                                        label.text = LEGEND_ICONS.products + ' ' + label.text;  // Icon sản phẩm
                                    }
                                });
                                
                                return labels;
                            }
                        }
                    },
                    title: {
                        display: true,
                        text: 'Tổng quan: 7 ngày trước'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = ((value / total) * 100).toFixed(1);
                                
                                if (label === 'Đơn hàng') {
                                    return `${label}: ${new Intl.NumberFormat('vi-VN').format(value)} đơn (${percentage}%)`;
                                } else {
                                    return `${label}: ${new Intl.NumberFormat('vi-VN').format(value)} sản phẩm (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            }
        };

        // Tạo pie chart
        const pieChart = new Chart(pieCtx, pieConfig);

        // Pie chart actions
        function randomizePieData() {
            pieChart.data.datasets[0].data = [
                getRandomNumber(50, 500), // Đơn hàng
                getRandomNumber(100, 2000)  // Sản phẩm
            ];
            pieChart.update();
        }

        function addPieDataset() {
            const newDataset = {
                label: 'Dataset ' + (pieChart.data.datasets.length + 1),
                data: [getRandomNumber(20, 200), getRandomNumber(50, 800)],
                backgroundColor: [
                    Object.values(CHART_COLORS)[pieChart.data.datasets.length % Object.values(CHART_COLORS).length],
                    Object.values(CHART_COLORS)[(pieChart.data.datasets.length + 1) % Object.values(CHART_COLORS).length]
                ],
                borderWidth: 2,
                borderColor: '#fff'
            };
            pieChart.data.datasets.push(newDataset);
            pieChart.update();
        }

        function addPieData() {
            const newLabels = ['Đơn hủy', 'Đơn hoàn', 'Khách hàng mới', 'Doanh thu'];
            const currentLength = pieChart.data.labels.length;
            
            if (currentLength < newLabels.length + 2) {
                pieChart.data.labels.push(newLabels[currentLength - 2]);
                pieChart.data.datasets[0].data.push(getRandomNumber(10, 300));
                pieChart.data.datasets[0].backgroundColor.push(Object.values(CHART_COLORS)[currentLength % Object.values(CHART_COLORS).length]);
                pieChart.update();
            }
        }

        // Export pie chart actions
        window.pieChartActions = {
            randomizeData: randomizePieData,
            addDataset: addPieDataset,
            addData: addPieData
        };

        // Store chart instances globally for time filter to access
        window.revenueChart = revenueChart;
        window.pieChart = pieChart;

        // Initialize charts with 7 days data (default)
        updateLineChart('7days');
        updatePieChart('7days');
    });
</script>

<!-- Time filter and date picker functionality -->
<script src="{{ asset('assets/js/chart-time-filter.js') }}"></script> 