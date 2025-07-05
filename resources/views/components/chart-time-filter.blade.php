// ==================== TIME FILTER & CUSTOM DATE PICKER ====================

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

// Data for different time periods
const chartDataByPeriod = {
    today: {
        line: {
            labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
            totalBill: [10, 25, 40, 85, 120, 95, 60],
            totalDropship: [2, 5, 8, 15, 22, 18, 12]
        },
        pie: {
            data: [12, 45],
            title: 'Tổng quan: Hôm nay'
        }
    },
    yesterday: {
        line: {
            labels: ['00:00', '04:00', '08:00', '12:00', '16:00', '20:00', '24:00'],
            totalBill: [15, 30, 45, 90, 130, 100, 80],
            totalDropship: [3, 6, 9, 18, 25, 20, 16]
        },
        pie: {
            data: [18, 67],
            title: 'Tổng quan: Hôm qua'
        }
    },
    '7days': {
        line: {
            labels: getLast7DaysLabels(),
            totalBill: [120, 190, 300, 500, 200, 300, 450],
            totalDropship: [20, 35, 45, 80, 40, 60, 75]
        },
        pie: {
            data: [85, 320],
            title: 'Tổng quan: 7 ngày trước'
        }
    },
    '30days': {
        line: {
            labels: ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'],
            totalBill: [2800, 3200, 2950, 3400],
            totalDropship: [480, 550, 510, 580]
        },
        pie: {
            data: [342, 1280],
            title: 'Tổng quan: 30 ngày trước'
        }
    },
    thismonth: {
        line: {
            labels: ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'],
            totalBill: [2500, 2800, 3100, 2900],
            totalDropship: [420, 480, 530, 490]
        },
        pie: {
            data: [298, 1150],
            title: 'Tổng quan: Tháng này'
        }
    },
    lastmonth: {
        line: {
            labels: ['Tuần 1', 'Tuần 2', 'Tuần 3', 'Tuần 4'],
            totalBill: [2200, 2600, 2400, 2800],
            totalDropship: [380, 440, 410, 470]
        },
        pie: {
            data: [256, 980],
            title: 'Tổng quan: Tháng trước'
        }
    }
};

// Function to update line chart
function updateLineChart(period) {
    if (window.revenueChart) {
        const data = chartDataByPeriod[period].line;
        window.revenueChart.data.labels = data.labels;
        window.revenueChart.data.datasets[0].data = data.totalBill;
        window.revenueChart.data.datasets[1].data = data.totalDropship;
        window.revenueChart.update();
    }
}

// Function to update pie chart
function updatePieChart(period) {
    if (window.pieChart) {
        const data = chartDataByPeriod[period].pie;
        window.pieChart.data.datasets[0].data = data.data;
        window.pieChart.options.plugins.title.text = data.title;
        window.pieChart.update();
    }
}

// Function to get period text
function getPeriodText(period) {
    const periodTexts = {
        'today': 'Hôm nay',
        'yesterday': 'Hôm qua',
        '7days': '7 ngày trước',
        '30days': '30 ngày trước',
        'thismonth': 'Tháng này',
        'lastmonth': 'Tháng trước'
    };
    return periodTexts[period] || '7 ngày trước';
}

// Chart time filter event listeners
document.addEventListener('click', function(e) {
    if (e.target.classList.contains('chart-time-filter')) {
        e.preventDefault();
        
        const period = e.target.getAttribute('data-period');
        const periodText = getPeriodText(period);
        
        // Update charts immediately
        updateLineChart(period);
        updatePieChart(period);
        
        // Update dropdown texts
        const lineChartDateElement = document.getElementById('customLineChartDate');
        const pieChartDateElement = document.getElementById('customPieChartDate');
        
        if (lineChartDateElement) {
            lineChartDateElement.innerHTML = periodText + ' <i class="mdi mdi-chevron-down ms-1"></i>';
        }
        if (pieChartDateElement) {
            pieChartDateElement.innerHTML = periodText + ' <i class="mdi mdi-chevron-down ms-1"></i>';
        }
        
        // Close modal
        const modal = document.getElementById('customDateModal');
        if (modal) {
            bootstrap.Modal.getInstance(modal)?.hide();
        }
    }
});

// Custom date picker functionality
document.addEventListener('click', function(e) {
    if (e.target.id === 'customLineChartDate') {
        e.preventDefault();
        openCustomDatePicker('line');
    }
    if (e.target.id === 'customPieChartDate') {
        e.preventDefault();
        openCustomDatePicker('pie');
    }
});

// Function to open custom date picker
function openCustomDatePicker(chartType) {
    // Create modal if not exists
    let modal = document.getElementById('customDateModal');
    if (!modal) {
        modal = createCustomDateModal();
        document.body.appendChild(modal);
    }
    
    // Reset date range picker
    if (window.dateRangePicker) {
        window.dateRangePicker.fromDate = null;
        window.dateRangePicker.toDate = null;
        window.dateRangePicker.hoveredDate = null;
    }
    
    // Set current chart type
    modal.dataset.chartType = chartType;
    
    // Re-initialize calendars
    setTimeout(() => {
        initializeDateRangePicker();
        
        // Reset to default 7 days state  
        updateLineChart('7days');
        updatePieChart('7days');
        const defaultText = '7 ngày trước';
        const lineElement = document.getElementById('customLineChartDate');
        const pieElement = document.getElementById('customPieChartDate');
        if (lineElement) lineElement.innerHTML = defaultText + ' <i class="mdi mdi-chevron-down ms-1"></i>';
        if (pieElement) pieElement.innerHTML = defaultText + ' <i class="mdi mdi-chevron-down ms-1"></i>';
    }, 200);
    
    // Show modal
    const bootstrapModal = new bootstrap.Modal(modal);
    bootstrapModal.show();
}

// Function to create custom date modal
function createCustomDateModal() {
    const modalHTML = `
        <div class="modal fade" id="customDateModal" tabindex="-1" aria-labelledby="customDateModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-base" style="max-width: 600px;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="customDateModalLabel">Chọn khoảng thời gian</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                    </div>
                    <div class="modal-body" style="padding: 0.5rem;">
                        <form id="customDateForm">                     
                            <div class="date-range-picker" id="dateRangePicker">
                                <div class="row flex-nowrap">
                                    <div class="col-md-6">
                                        <div class="selected-range-inline mb-2">
                                           
                                            <a class="dropdown-item chart-time-filter" href="#" data-period="7days" data-chart="line">7 ngày trước</a>
                                            <a class="dropdown-item chart-time-filter" href="#" data-period="30days" data-chart="line">30 ngày trước</a>
                                            <a class="dropdown-item chart-time-filter" href="#" data-period="thismonth" data-chart="line">Tháng này</a>
                                            <a class="dropdown-item chart-time-filter" href="#" data-period="lastmonth" data-chart="line">Tháng trước</a>
                                            <div class="dropdown-divider"></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="month-calendar" id="leftMonth"></div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="month-calendar" id="rightMonth"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        </div>
                        <div class="modal-footer p-3">
                            <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Đóng</button>
                        </div>
                    
                 </div>
            </div>
        </div>
    `;
    
    const modalElement = document.createElement('div');
    modalElement.innerHTML = modalHTML;
    
    // Initialize date range picker after modal is created
    setTimeout(() => {
        initializeDateRangePicker();
    }, 100);
    
    return modalElement.firstElementChild;
}

// Date range picker variables
window.dateRangePicker = {
    fromDate: null,
    toDate: null,
    hoveredDate: null,
    currentLeftMonth: new Date(),
    currentRightMonth: new Date()
};

// Initialize date range picker
function initializeDateRangePicker() {
    const today = new Date();
    window.dateRangePicker.currentLeftMonth = new Date(today.getFullYear(), today.getMonth(), 1);
    window.dateRangePicker.currentRightMonth = new Date(today.getFullYear(), today.getMonth() + 1, 1);
    
    renderCalendar('leftMonth', window.dateRangePicker.currentLeftMonth);
    renderCalendar('rightMonth', window.dateRangePicker.currentRightMonth);
    
    updateDateDisplay();
}

// Render calendar for a specific month
function renderCalendar(containerId, date) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const year = date.getFullYear();
    const month = date.getMonth();
    const firstDay = new Date(year, month, 1);
    const lastDay = new Date(year, month + 1, 0);
    const startDate = new Date(firstDay);
    startDate.setDate(startDate.getDate() - firstDay.getDay());

    // Create header
    const header = document.createElement('div');
    header.className = 'calendar-header';
    header.innerHTML = `
        <button type="button" class="calendar-nav-btn" onclick="navigateMonth('${containerId}', -1)">
            <i class="mdi mdi-chevron-left"></i>
        </button>
        <div class="calendar-title">
            ${date.toLocaleDateString('vi-VN', { month: 'long', year: 'numeric' })}
        </div>
        <button type="button" class="calendar-nav-btn" onclick="navigateMonth('${containerId}', 1)">
            <i class="mdi mdi-chevron-right"></i>
        </button>
    `;

    // Create day headers
    const dayHeaders = ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'];
    const grid = document.createElement('div');
    grid.className = 'calendar-grid';

    // Add day headers
    dayHeaders.forEach(day => {
        const dayHeader = document.createElement('div');
        dayHeader.className = 'calendar-day-header';
        dayHeader.textContent = day;
        grid.appendChild(dayHeader);
    });

    // Add calendar days
    for (let i = 0; i < 42; i++) { // 6 weeks * 7 days
        const currentDate = new Date(startDate);
        currentDate.setDate(startDate.getDate() + i);
        
        const dayElement = document.createElement('div');
        dayElement.className = 'custom-day';
        dayElement.textContent = currentDate.getDate();
        dayElement.dataset.date = currentDate.toISOString().split('T')[0];
        
        // Add classes based on date state
        if (currentDate.getMonth() !== month) {
            dayElement.classList.add('outside-month');
        }
        
        updateDayClasses(dayElement, currentDate);
        
        // Add event listeners
        dayElement.addEventListener('click', () => onDateSelection(currentDate));
        dayElement.addEventListener('mouseenter', () => {
            window.dateRangePicker.hoveredDate = currentDate;
            updateAllCalendars();
        });
        dayElement.addEventListener('mouseleave', () => {
            window.dateRangePicker.hoveredDate = null;
            updateAllCalendars();
        });
        
        grid.appendChild(dayElement);
    }

    container.innerHTML = '';
    container.appendChild(header);
    container.appendChild(grid);
}

// Update day classes based on selection state
function updateDayClasses(dayElement, date) {
    const dateStr = date.toISOString().split('T')[0];
    const fromDateStr = window.dateRangePicker.fromDate ? window.dateRangePicker.fromDate.toISOString().split('T')[0] : null;
    const toDateStr = window.dateRangePicker.toDate ? window.dateRangePicker.toDate.toISOString().split('T')[0] : null;
    const hoveredDateStr = window.dateRangePicker.hoveredDate ? window.dateRangePicker.hoveredDate.toISOString().split('T')[0] : null;

    // Reset classes
    dayElement.classList.remove('focused', 'range', 'faded', 'start-range', 'end-range', 'in-range');

    // Check if this day is selected
    if (fromDateStr === dateStr || toDateStr === dateStr) {
        dayElement.classList.add('focused');
    }

    // Check if this day is in range
    if (fromDateStr && toDateStr) {
        if (isDateInRange(date, window.dateRangePicker.fromDate, window.dateRangePicker.toDate)) {
            if (fromDateStr === dateStr) {
                dayElement.classList.add('start-range');
            } else if (toDateStr === dateStr) {
                dayElement.classList.add('end-range');
            } else {
                dayElement.classList.add('in-range');
            }
        }
    } else if (fromDateStr && hoveredDateStr && isDateInRange(date, window.dateRangePicker.fromDate, window.dateRangePicker.hoveredDate)) {
        dayElement.classList.add('faded');
    }
}

// Handle date selection
function onDateSelection(selectedDate) {
    if (!window.dateRangePicker.fromDate || (window.dateRangePicker.fromDate && window.dateRangePicker.toDate)) {
        // Start new selection
        window.dateRangePicker.fromDate = selectedDate;
        window.dateRangePicker.toDate = null;
    } else {
        // Complete the range
        if (selectedDate < window.dateRangePicker.fromDate) {
            window.dateRangePicker.toDate = window.dateRangePicker.fromDate;
            window.dateRangePicker.fromDate = selectedDate;
        } else {
            window.dateRangePicker.toDate = selectedDate;
        }
        
        // Update charts immediately when range is complete
        if (window.dateRangePicker.fromDate && window.dateRangePicker.toDate) {
            const startDateStr = window.dateRangePicker.fromDate.toISOString().split('T')[0];
            const endDateStr = window.dateRangePicker.toDate.toISOString().split('T')[0];
            const customData = generateCustomDateData(startDateStr, endDateStr);
            const dateRangeText = formatDateRange(startDateStr, endDateStr);
            
            // Update both charts
            updateLineChartWithCustomData(customData.line);
            updatePieChartWithCustomData(customData.pie);
            
            // Update dropdown texts
            const lineElement = document.getElementById('customLineChartDate');
            const pieElement = document.getElementById('customPieChartDate');
            if (lineElement) lineElement.innerHTML = dateRangeText + ' <i class="mdi mdi-chevron-down ms-1"></i>';
            if (pieElement) pieElement.innerHTML = dateRangeText + ' <i class="mdi mdi-chevron-down ms-1"></i>';
            
            // Close modal after a short delay to show selection
            setTimeout(() => {
                const modal = document.getElementById('customDateModal');
                if (modal) {
                    bootstrap.Modal.getInstance(modal)?.hide();
                }
            }, 500);
        }
    }
    
    updateAllCalendars();
    updateDateDisplay();
}

// Update all calendars
function updateAllCalendars() {
    const leftContainer = document.getElementById('leftMonth');
    const rightContainer = document.getElementById('rightMonth');
    
    if (leftContainer) {
        const days = leftContainer.querySelectorAll('.custom-day');
        days.forEach(dayElement => {
            const date = new Date(dayElement.dataset.date);
            updateDayClasses(dayElement, date);
        });
    }
    
    if (rightContainer) {
        const days = rightContainer.querySelectorAll('.custom-day');
        days.forEach(dayElement => {
            const date = new Date(dayElement.dataset.date);
            updateDayClasses(dayElement, date);
        });
    }
}

// Navigate months
function navigateMonth(containerId, direction) {
    if (containerId === 'leftMonth') {
        window.dateRangePicker.currentLeftMonth.setMonth(window.dateRangePicker.currentLeftMonth.getMonth() + direction);
        renderCalendar('leftMonth', window.dateRangePicker.currentLeftMonth);
    } else if (containerId === 'rightMonth') {
        window.dateRangePicker.currentRightMonth.setMonth(window.dateRangePicker.currentRightMonth.getMonth() + direction);
        renderCalendar('rightMonth', window.dateRangePicker.currentRightMonth);
    }
}

// Check if date is in range
function isDateInRange(date, start, end) {
    if (!start || !end) return false;
    return date >= start && date <= end;
}

// Update date display
function updateDateDisplay() {
    const fromDisplay = document.getElementById('fromDateDisplay');
    const toDisplay = document.getElementById('toDateDisplay');
    
    if (fromDisplay) {
        fromDisplay.textContent = window.dateRangePicker.fromDate 
            ? JSON.stringify(window.dateRangePicker.fromDate.toISOString().split('T')[0]).replace(/"/g, '') 
            : 'null';
    }
    
    if (toDisplay) {
        toDisplay.textContent = window.dateRangePicker.toDate 
            ? JSON.stringify(window.dateRangePicker.toDate.toISOString().split('T')[0]).replace(/"/g, '') 
            : 'null';
    }
}

// Make navigateMonth available globally
window.navigateMonth = navigateMonth;

// Function to generate custom data based on date range
function generateCustomDateData(startDate, endDate) {
    const start = new Date(startDate);
    const end = new Date(endDate);
    const daysDiff = Math.ceil((end - start) / (1000 * 60 * 60 * 24)) + 1;
    
    // Generate labels based on date range
    const labels = [];
    const lineData = [];
    const lineDropshipData = [];
    
    for (let i = 0; i < daysDiff; i++) {
        const currentDate = new Date(start);
        currentDate.setDate(start.getDate() + i);
        labels.push(currentDate.toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' }));
        
        // Generate random data for demo
        lineData.push(Math.floor(Math.random() * (800 - 50 + 1)) + 50);
        lineDropshipData.push(Math.floor(Math.random() * (150 - 10 + 1)) + 10);
    }

    // Generate pie data
    const totalOrders = Math.floor(Math.random() * (daysDiff * 20 - daysDiff * 5 + 1)) + daysDiff * 5;
    const totalProducts = Math.floor(Math.random() * (daysDiff * 60 - daysDiff * 15 + 1)) + daysDiff * 15;

    return {
        line: {
            labels: labels,
            totalBill: lineData,
            totalDropship: lineDropshipData
        },
        pie: {
            data: [totalOrders, totalProducts],
            title: `Tùy chọn: ${formatDateRange(startDate, endDate)}`
        }
    };
}

// Function to format date range text
function formatDateRange(startDate, endDate) {
    const start = new Date(startDate).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
    const end = new Date(endDate).toLocaleDateString('vi-VN', { day: '2-digit', month: '2-digit' });
    return `${start} - ${end}`;
}

// Function to update line chart with custom data
function updateLineChartWithCustomData(customData) {
    if (window.revenueChart) {
        window.revenueChart.data.labels = customData.labels;
        window.revenueChart.data.datasets[0].data = customData.totalBill;
        window.revenueChart.data.datasets[1].data = customData.totalDropship;
        window.revenueChart.update();
    }
}

// Function to update pie chart with custom data
function updatePieChartWithCustomData(customData) {
    if (window.pieChart) {
        window.pieChart.data.datasets[0].data = customData.data;
        window.pieChart.options.plugins.title.text = customData.title;
        window.pieChart.update();
    }
} 