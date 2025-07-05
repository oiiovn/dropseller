{{-- Component: Ads Filters --}}
<div class="filters-container">
    <div class="d-flex gap-2 align-items-end flex-wrap w-100">
        <!-- <div class="flex-fill" style="max-width: 220px;">
            <input type="text" class="form-control" id="searchFilter" placeholder="Tìm kiếm...">
        </div> -->
        <!-- <div class="flex-fill" style="max-width: 220px;">
            <input type="date" class="form-control" id="dateRangeFilter" placeholder="Chọn khoảng thời gian">
        </div>
        <div class="flex-fill" style="max-width: 180px;">
            <select class="form-select" id="paymentStatusFilter">
                <option value="">Tất cả trạng thái</option>
                <option value="Chưa thanh toán">Chưa thanh toán</option>
                <option value="Đã thanh toán">Đã thanh toán</option>
            </select>
        </div>
       
        <button type="button" class="btn btn-primary" id="applyFilters">
            <i class="ri-filter-line me-1"></i>Lọc
        </button>
        <button type="button" class="btn btn-outline-secondary" id="clearFilters">
            <i class="ri-refresh-line"></i>
        </button> -->
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize date range picker
        $('#dateRangeFilter').daterangepicker({
            locale: {
                format: 'DD/MM/YYYY',
                applyLabel: 'Áp dụng',
                cancelLabel: 'Hủy',
                fromLabel: 'Từ',
                toLabel: 'Đến',
                customRangeLabel: 'Tùy chỉnh',
                daysOfWeek: ['CN', 'T2', 'T3', 'T4', 'T5', 'T6', 'T7'],
                monthNames: ['Tháng 1', 'Tháng 2', 'Tháng 3', 'Tháng 4', 'Tháng 5', 'Tháng 6', 'Tháng 7', 'Tháng 8', 'Tháng 9', 'Tháng 10', 'Tháng 11', 'Tháng 12']
            },
            ranges: {
                'Hôm nay': [moment(), moment()],
                'Hôm qua': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                '7 ngày trước': [moment().subtract(6, 'days'), moment()],
                '30 ngày trước': [moment().subtract(29, 'days'), moment()],
                'Tháng này': [moment().startOf('month'), moment().endOf('month')],
                'Tháng trước': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            }
        });

        // Apply filters
        $('#applyFilters').on('click', function() {
            const dateRange = $('#dateRangeFilter').val();
            const paymentStatus = $('#paymentStatusFilter').val();
            const searchTerm = $('#searchFilter').val();
            
            // Apply filters to DataTable
            const table = $('#adsTable').DataTable();
            
            // Custom filtering function
            $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                let show = true;
                
                // Date range filter
                if (dateRange) {
                    const dateParts = dateRange.split(' - ');
                    const startDate = moment(dateParts[0], 'DD/MM/YYYY');
                    const endDate = moment(dateParts[1], 'DD/MM/YYYY');
                    const rowDate = moment(data[2], 'DD/MM/YYYY'); // Assuming date is in column 2
                    
                    if (!rowDate.isBetween(startDate, endDate, 'day', '[]')) {
                        show = false;
                    }
                }
                
                // Payment status filter
                if (paymentStatus && data[6] !== paymentStatus) { // Assuming payment status is in column 6
                    show = false;
                }
                
                // Search filter
                if (searchTerm) {
                    const searchLower = searchTerm.toLowerCase();
                    const rowText = data.join(' ').toLowerCase();
                    if (!rowText.includes(searchLower)) {
                        show = false;
                    }
                }
                
                return show;
            });
            
            table.draw();
        });

        // Clear filters
        $('#clearFilters').on('click', function() {
            $('#dateRangeFilter').val('');
            $('#paymentStatusFilter').val('');
            $('#searchFilter').val('');
            
            // Remove custom filtering
            $.fn.dataTable.ext.search.pop();
            
            // Redraw table
            $('#adsTable').DataTable().draw();
        });

        // Search on Enter key
        $('#searchFilter').on('keypress', function(e) {
            if (e.which === 13) {
                $('#applyFilters').click();
            }
        });
    });
</script>