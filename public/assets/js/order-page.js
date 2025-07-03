// Tắt toàn bộ console log, warn, error, info
// console.log = function() {};
// console.warn = function() {};
// console.error = function() {};
// console.info = function() {};

// Order Page JavaScript

// Add viewport meta if not present
if (!document.querySelector('meta[name="viewport"]')) {
    const viewport = document.createElement('meta');
    viewport.name = 'viewport';
    viewport.content = 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no';
    document.head.appendChild(viewport);
    console.log('📱 Mobile Fix: Viewport meta added');
}

// Main Order Page Functions
class OrderPage {
    constructor() {
        this.mainTable = null;
        this.filterVisible = false;
        this.init();
    }

    init() {
        this.initDataTables();
        this.initFilters();
        this.initMobileFilter();
        this.initOrderModal();
        this.initCopyFunctionality();
        this.initDebugFunctions();
    }

    initDataTables() {
        if ($(window).width() > 768) {
            // Check if DataTable is already initialized
            if (!$.fn.DataTable.isDataTable('#orderTable')) {
                this.mainTable = $('#orderTable').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": false,
                    "info": true,
                    "lengthMenu": [20, 50, 100, 150],
                    "pageLength": 20,
                    "dom": "t<'pagination-container'<'row'<'col-4'l><'col-4 text-center'i><'col-4'p>>>",
                    "order": [[2, "desc"]],
                    "language": {
                        "lengthMenu": "Hiển thị _MENU_ đơn hàng",
                        "zeroRecords": "Không tìm thấy dữ liệu",
                        "info": "Hiển thị _START_ đến _END_ của _TOTAL_ đơn hàng",
                        "infoEmpty": "Không có dữ liệu để hiển thị",
                        "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                        "search": "",
                        "paginate": {
                            "first": "Đầu",
                            "last": "Cuối", 
                            "next": "Tiếp",
                            "previous": "Trước"
                        }
                    },
                    "initComplete": function() {
                        $('#orderTable').show();
                        console.log('✅ Main DataTable initialized');
                    }
                });
            } else {
                // If already initialized, just get the instance
                this.mainTable = $('#orderTable').DataTable();
                $('#orderTable').show();
                console.log('✅ Main DataTable already initialized, using existing instance');
            }
        }
    }

    initFilters() {
        // Apply filters button
        $('#applyFilters').on('click', () => {
            this.applyMainFilters();
            this.autoCloseMobileFilter();
        });

        // Clear filters button
        $('#clearFilters').on('click', () => {
            this.clearMainFilters();
            this.autoCloseMobileFilter();
        });

        // Custom search
        $('#customSearch').on('keyup', () => {
            this.applyMainFilters();
        });

        // Auto apply filters when selects change
        $('#paymentFilter, #reconciledFilter').on('change', () => {
            this.applyMainFilters();
        });

        $('#dateFilter').on('change', () => {
            this.applyMainFilters();
        });
    }

    applyMainFilters() {
        const dateFilter = $('#dateFilter').val();
        const paymentFilter = $('#paymentFilter').val();
        const reconciledFilter = $('#reconciledFilter').val();
        const searchFilter = $('#customSearch').val().toLowerCase();
        
        console.log('🔍 Applying main filters:', {
            date: dateFilter,
            payment: paymentFilter,
            reconciled: reconciledFilter,
            search: searchFilter
        });

        $('#orderTable tbody tr').each(function() {
            const $row = $(this);
            let show = true;

            // Date filter
            if (dateFilter) {
                const filterDate = $row.find('.order-code-date').text().trim();
                const createdAtText = $row.find('td').eq(2).find('small').text().trim();
                const createdAtDate = createdAtText.split(' ')[0];
                
                // Convert yyyy-mm-dd to d/m/Y
                const parts = dateFilter.split('-');
                const formattedDate = parts[2] + '/' + parts[1] + '/' + parts[0];
                
                if (filterDate !== formattedDate && createdAtDate !== formattedDate) {
                    show = false;
                }
            }

            // Payment status filter
            if (paymentFilter && show) {
                const paymentStatus = $row.find('.status-pill').first().text().trim();
                if (paymentStatus !== paymentFilter) {
                    show = false;
                }
            }

            // Reconciled filter
            if (reconciledFilter && show) {
                const statusPills = $row.find('.status-pill');
                let reconciledStatus = '';
                if (statusPills.length > 1) {
                    reconciledStatus = statusPills.eq(1).text().trim();
                }
                if (reconciledStatus !== reconciledFilter) {
                    show = false;
                }
            }

            // Search filter
            if (searchFilter && show) {
                const orderCode = $row.find('.order-link').first().text().trim().toLowerCase();
                const shopName = $row.find('.shop-name').text().trim().toLowerCase();
                if (!orderCode.includes(searchFilter) && !shopName.includes(searchFilter)) {
                    show = false;
                }
            }

            if (show) {
                $row.show();
            } else {
                $row.hide();
            }
        });
        
        // Update pagination info and show/hide no data message
        const visibleRows = $('#orderTable tbody tr:visible').length;
        const totalRows = $('#orderTable tbody tr').length;
        console.log(`✅ Showing ${visibleRows} of ${totalRows} rows after filtering`);
        
        // Update DataTable pagination info manually
        if (this.mainTable) {
            const info = this.mainTable.page.info();
            const start = info.start + 1;
            const end = Math.min(info.start + visibleRows, info.recordsTotal);
            
            // Update the info text
            $('.dataTables_info').html(
                `Hiển thị ${start} đến ${end} của ${visibleRows} đơn hàng`
            );
            
            // Update pagination controls
            if (visibleRows <= info.length) {
                $('.dataTables_paginate').hide();
            } else {
                $('.dataTables_paginate').show();
            }
        }
        
        // Show/hide no data message
        if (visibleRows === 0) {
            $('#noDataMessage').show();
        } else {
            $('#noDataMessage').hide();
        }
        
        // Also handle mobile cards filtering  
        if ($(window).width() <= 768) {
            this.filterMobileCards();
        }
    }

    filterMobileCards() {
        let mobileVisibleCount = 0;
        $('.mobile-order-card').each(function() {
            const $card = $(this);
            let show = true;
            
            // Apply same filters to mobile cards
            const cardFilterDate = $card.data('filter-date');
            const cardPaymentStatus = $card.data('payment-status');
            const cardReconciledStatus = $card.data('reconciled');
            const cardOrderCode = $card.data('order-code').toLowerCase();
            const cardShopName = $card.data('shop-name').toLowerCase();
            
            const dateFilter = $('#dateFilter').val();
            const paymentFilter = $('#paymentFilter').val();
            const reconciledFilter = $('#reconciledFilter').val();
            const searchFilter = $('#customSearch').val().toLowerCase();
            
            if (dateFilter) {
                const parts = dateFilter.split('-');
                const formattedDate = parts[2] + '/' + parts[1] + '/' + parts[0];
                if (cardFilterDate !== formattedDate) {
                    show = false;
                }
            }
            
            if (paymentFilter && cardPaymentStatus !== paymentFilter) {
                show = false;
            }
            
            if (reconciledFilter && cardReconciledStatus !== reconciledFilter) {
                show = false;
            }
            
            if (searchFilter && !cardOrderCode.includes(searchFilter) && !cardShopName.includes(searchFilter)) {
                show = false;
            }
            
            if (show) {
                $card.show();
                mobileVisibleCount++;
            } else {
                $card.hide();
            }
        });
        
        // Show/hide no data message for mobile too
        if (mobileVisibleCount === 0) {
            $('#noDataMessage').show();
        } else {
            $('#noDataMessage').hide();
        }
    }

    clearMainFilters() {
        console.log('🧹 Clearing main filters...');
        $('#dateFilter').val('');
        $('#paymentFilter').val('');
        $('#reconciledFilter').val('');
        $('#customSearch').val('');
        $('#orderTable tbody tr').show();
        $('#noDataMessage').hide();
        $('.mobile-order-card').show();
        
        // Reset DataTable pagination info
        if (this.mainTable) {
            const info = this.mainTable.page.info();
            const totalRows = $('#orderTable tbody tr').length;
            const start = info.start + 1;
            const end = Math.min(info.start + info.length, totalRows);
            
            // Update the info text back to original
            $('.dataTables_info').html(
                `Hiển thị ${start} đến ${end} của ${totalRows} đơn hàng`
            );
            
            // Show pagination controls
            $('.dataTables_paginate').show();
        }
    }

    initMobileFilter() {
        // Mobile filter toggle functionality
        $('#mobileFilterToggle').on('click touchstart', (e) => {
            e.preventDefault();
            this.showMobileFilter();
        });

        // Hide mobile filter when clicking overlay
        $('#mobileFilterOverlay').on('click touchstart', (e) => {
            e.preventDefault();
            this.hideMobileFilter();
        });
        
        // Hide mobile filter when clicking close button
        $(document).on('click touchstart', '#mobileFilterClose', (e) => {
            e.preventDefault();
            this.hideMobileFilter();
        });
        
        // Hide mobile filter with ESC key
        $(document).on('keydown', (e) => {
            if (e.key === 'Escape' && this.filterVisible) {
                this.hideMobileFilter();
            }
        });

        // Handle resize
        $(window).on('resize', () => {
            if ($(window).width() <= 768) {
                $('#mobileFilterToggle').show();
            } else {
                $('#mobileFilterToggle').hide();
                this.hideMobileFilter();
            }
        });
    }

    showMobileFilter() {
        this.filterVisible = true;
        $('.filter-section').addClass('mobile-show');
        $('#mobileFilterOverlay').addClass('show');
        
        // Add close button if not exists
        if (!$('.mobile-filter-close').length) {
            $('.filter-section.mobile-show').prepend('<button class="mobile-filter-close" id="mobileFilterClose"><i class="ri-close-line"></i></button>');
        }
        
        // Add title if not exists
        if (!$('.mobile-filter-title').length) {
            $('.filter-section.mobile-show').prepend('<div class="mobile-filter-title">Bộ lọc</div>');
        }
        
        // Add labels to inputs for better UX
        $('.filter-section.mobile-show').find('input[type="date"]').before('<label>Chọn ngày:</label>');
        $('.filter-section.mobile-show').find('select').each(function() {
            let placeholder = $(this).find('option:first').text();
            $(this).before('<label>' + placeholder + ':</label>');
        });
        $('.filter-section.mobile-show').find('input[type="text"]').before('<label>Tìm kiếm:</label>');
        
        // Prevent body scroll
        $('body').addClass('modal-open').css('overflow', 'hidden');
        
        // Focus on first input after animation
        setTimeout(() => {
            $('.filter-section.mobile-show input:first').focus();
        }, 350);
        
        console.log('📱 Mobile filter opened');
    }

    hideMobileFilter() {
        this.filterVisible = false;
        $('.filter-section').removeClass('mobile-show');
        $('#mobileFilterOverlay').removeClass('show');
        
        // Remove all added elements
        $('.mobile-filter-close, .mobile-filter-title').remove();
        $('.filter-section label').remove(); // Remove labels added for mobile
        
        // Restore body scroll
        $('body').removeClass('modal-open').css('overflow', '');
        
        // Blur any focused input
        if (document.activeElement) {
            document.activeElement.blur();
        }
        
        console.log('📱 Mobile filter closed');
    }

    autoCloseMobileFilter() {
        if ($(window).width() <= 768 && this.filterVisible) {
            setTimeout(() => {
                this.hideMobileFilter();
            }, 500);
        }
    }

    initOrderModal() {
        console.log('🔧 Initializing order modal...');
        
        // Handle view order button click
        $(document).on('click', '.view-order-btn', function(e) {
            e.preventDefault();
            console.log('🎯 View order button clicked!');
            
            // Lấy data từ button
            const orderId = $(this).data('order-id');
            const orderCode = $(this).data('order-code');
            const shopName = $(this).data('shop-name');
            const filterDate = $(this).data('filter-date');
            const totalProducts = $(this).data('total-products');
            const totalDropship = $(this).data('total-dropship');
            const totalBill = $(this).data('total-bill');
            let orderDetails = [];
            try {
                const orderDetailsStr = $(this).data('order-details');
                console.log('🔍 Raw order details string:', orderDetailsStr);
                console.log('🔍 Type of order details:', typeof orderDetailsStr);
                
                if (orderDetailsStr) {
                    if (typeof orderDetailsStr === 'string') {
                        orderDetails = JSON.parse(orderDetailsStr);
                        console.log('✅ Successfully parsed order details from string');
                    } else if (Array.isArray(orderDetailsStr)) {
                        orderDetails = orderDetailsStr;
                        console.log('✅ Order details is already an array');
                    } else {
                        console.log('⚠️ Order details is neither string nor array:', orderDetailsStr);
                    }
                } else {
                    console.log('⚠️ No order details string found');
                }
            } catch (e) {
                console.error('❌ Error parsing order details:', e);
                console.error('❌ Error details:', e.message);
                console.error('❌ Raw data that failed to parse:', $(this).data('order-details'));
                orderDetails = [];
            }
            
            console.log('📊 Order data:', {
                orderId,
                orderCode,
                shopName,
                filterDate,
                totalProducts,
                totalDropship,
                totalBill,
                orderDetailsCount: orderDetails.length
            });

            // Đổ dữ liệu vào modal (nếu có các thẻ tương ứng)
            console.log('📝 Populating modal with order data...');
            
            if($('#modal-order-code').length) {
                $('#modal-order-code').text(orderCode);
                console.log('✅ Order code set:', orderCode);
            } else {
                console.log('⚠️ Modal order code element not found');
            }
            
            if($('#modal-shop-name').length) {
                $('#modal-shop-name').text(shopName);
                console.log('✅ Shop name set:', shopName);
            } else {
                console.log('⚠️ Modal shop name element not found');
            }
            
            if($('#modal-filter-date').length) {
                $('#modal-filter-date').text(filterDate);
                console.log('✅ Filter date set:', filterDate);
            } else {
                console.log('⚠️ Modal filter date element not found');
            }
            
            if($('#modal-total-products').length) {
                $('#modal-total-products').text(totalProducts + ' sản phẩm');
                console.log('✅ Total products set:', totalProducts);
            } else {
                console.log('⚠️ Modal total products element not found');
            }
            
            if($('#modal-total-dropship').length) {
                $('#modal-total-dropship').text(new Intl.NumberFormat('vi-VN').format(totalDropship) + ' đ');
                console.log('✅ Total dropship set:', totalDropship);
            } else {
                console.log('⚠️ Modal total dropship element not found');
            }
            
            if($('#modal-total-bill').length) {
                $('#modal-total-bill').text(new Intl.NumberFormat('vi-VN').format(totalBill) + ' đ');
                console.log('✅ Total bill set:', totalBill);
            } else {
                console.log('⚠️ Modal total bill element not found');
            }

            // Đổ chi tiết sản phẩm vào bảng trong modal
            console.log('📋 Processing order details:', orderDetails);
            
            let detailsHtml = '';
            if (orderDetails && orderDetails.length > 0) {
                console.log(`✅ Found ${orderDetails.length} order details`);
                orderDetails.forEach(function(detail, index) {
                    console.log(`📦 Detail ${index}:`, detail);
                    detailsHtml += `
                        <tr>
                            <td style="padding: 12px;">
                                <div class="d-flex align-items-center gap-3">
                                    <div class="flex-shrink-0">
                                        <img src="${detail.image || '/assets/images/default-product.png'}" alt="" 
                                             class="img-fluid rounded border" 
                                             style="width: 50px; height: 50px; object-fit: cover;"
                                             onerror="this.src='/assets/images/default-product.png'">
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold text-dark mb-1" style="font-size: 14px; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; text-overflow: ellipsis; white-space: normal;">
                                            <span class="order-detail-product-name">${detail.product_name || 'N/A'}</span>
                                        </div>
                                        <style>
                                            @media (max-width: 768px) {
                                                .order-detail-product-name {
                                                    font-size: 12px !important;
                                                }
                                            }
                                        </style>
                                        <div class="text-muted small">
                                            <i class="ri-barcode-line me-1"></i>SKU: <span class="fw-medium">${detail.sku || 'N/A'}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center fw-semibold" style="padding: 12px;">
                                <span class="badge bg-success-subtle text-success px-3 py-2">
                                    ${detail.quantity || 0}
                                </span>
                            </td>
                            <td class="text-center fw-semibold" style="padding: 12px; white-space: nowrap;">
                                ${new Intl.NumberFormat('vi-VN').format(detail.unit_cost || 0)} đ
                            </td>
                            <td class="text-center fw-bold text-primary" style="padding: 12px; white-space: nowrap;">
                                ${new Intl.NumberFormat('vi-VN').format(detail.total_cost || 0)} đ
                            </td>
                        </tr>
                    `;
                });
                $('#modal-no-data').hide();
                console.log('✅ Order details HTML generated');
            } else {
                console.log('⚠️ No order details found, showing no data message');
                $('#modal-no-data').show();
            }
            $('#modal-order-details').html(detailsHtml);
            console.log('✅ Order details HTML inserted into modal');
            console.log('📋 Final HTML length:', detailsHtml.length);
            console.log('📋 Final HTML preview:', detailsHtml.substring(0, 200) + '...');
            
            // Debug: Kiểm tra tbody sau khi set HTML
            setTimeout(() => {
                const tbody = $('#modal-order-details');
                console.log('🔍 Tbody element found:', tbody.length > 0);
                console.log('🔍 Tbody HTML content:', tbody.html());
                console.log('🔍 Tbody children count:', tbody.children().length);
            }, 100);

            // Mở modal
            console.log('🚀 Opening modal...');
            const modal = $('#orderDetailModal');
            console.log('Modal element:', modal.length > 0 ? 'Found' : 'Not found');
            
            if (modal.length > 0) {
                modal.modal('show');
                console.log('✅ Modal show() called');
            } else {
                console.error('❌ Modal element not found!');
            }
        });

        // Enhanced Modal Responsive
        $('#orderDetailModal').on('show.bs.modal', function() {
            if ($(window).width() <= 768) {
                $(this).find('.modal-dialog').addClass('modal-fullscreen-mobile');
            }
        });

        $('#orderDetailModal').on('hide.bs.modal', function() {
            $(this).find('.modal-dialog').removeClass('modal-fullscreen-mobile');
        });
    }

    initCopyFunctionality() {
        // Copy functionality for order codes (both desktop and mobile)
        $(document).on('click', '.order-link, .mobile-order-code', (e) => {
            e.preventDefault();
            
            let orderCode;
            if ($(e.currentTarget).hasClass('mobile-order-code')) {
                orderCode = $(e.currentTarget).data('order-code');
            } else {
                orderCode = $(e.currentTarget).data('order-code');
            }
            
            if (orderCode) {
                navigator.clipboard.writeText(orderCode).then(() => {
                    // Show toast notification
                    this.showCopyNotification('Đã copy: ' + orderCode);
                }).catch(() => {
                    console.log('Copy failed, showing fallback');
                    // Fallback for older browsers
                    const tempInput = document.createElement('input');
                    tempInput.value = orderCode;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    document.execCommand('copy');
                    document.body.removeChild(tempInput);
                    this.showCopyNotification('Đã copy: ' + orderCode);
                });
            }
        });

        // Copy order information
        $('#modal-copy-info').on('click', () => {
            const orderCode = $('#modal-order-code').text();
            const shopName = $('#modal-shop-name').text();
            const filterDate = $('#modal-filter-date').text();
            const totalProducts = $('#modal-total-products').text();
            const totalBill = $('#modal-total-bill').text();
            
            const copyText = `Mã đơn: ${orderCode}\nShop: ${shopName}\nNgày: ${filterDate}\nSố lượng: ${totalProducts}\nTổng tiền: ${totalBill}`;
            
            navigator.clipboard.writeText(copyText).then(() => {
                // Show success message
                const btn = $('#modal-copy-info');
                const originalText = btn.html();
                btn.html('<i class="ri-check-line me-1"></i>Đã copy!').addClass('btn-success').removeClass('btn-primary');
                setTimeout(() => {
                    btn.html(originalText).removeClass('btn-success').addClass('btn-primary');
                }, 2000);
            }).catch(() => {
                alert('Không thể copy. Vui lòng copy thủ công.');
            });
        });
    }

    showCopyNotification(message) {
        // Remove existing notifications
        $('.copy-notification').remove();
        
        // Create notification
        const notification = $(`
            <div class="copy-notification position-fixed" style="
                top: 20px; 
                right: 20px; 
                background: linear-gradient(135deg, #4285f4 0%, #1a73e8 100%); 
                color: white; 
                padding: 12px 20px; 
                border-radius: 8px; 
                box-shadow: 0 4px 20px rgba(66, 133, 244, 0.3);
                z-index: 9999;
                font-weight: 500;
                transform: translateX(100%);
                transition: all 0.3s ease;
            ">
                <i class="ri-checkbox-circle-line me-2"></i>${message}
            </div>
        `);
        
        $('body').append(notification);
        
        // Animate in
        setTimeout(() => {
            notification.css('transform', 'translateX(0)');
        }, 100);
        
        // Auto remove
        setTimeout(() => {
            notification.css('transform', 'translateX(100%)');
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }

    initDebugFunctions() {
        // Debug function to test filtering
        window.debugFiltering = () => {
            console.log('=== FILTER DEBUG ===');
            
            // Main table
            const mainDateFilter = document.getElementById('dateFilter');
            const mainPaymentFilter = document.getElementById('paymentFilter'); 
            const mainReconciledFilter = document.getElementById('reconciledFilter');
            
            console.log('Main filters:', {
                date: mainDateFilter?.value,
                payment: mainPaymentFilter?.value,
                reconciled: mainReconciledFilter?.value
            });
            
            // Check first few rows data
            const mainRows = document.querySelectorAll('#orderTable tbody tr');
            console.log('Total rows in table:', mainRows.length);
            
            mainRows.forEach((row, index) => {
                if (index < 3) { // Only first 3 rows
                    const orderCodeDate = row.querySelector('.order-code-date')?.textContent.trim();
                    const createdAt = row.querySelector('td:nth-child(3) small')?.textContent.trim();
                    const paymentStatusElements = row.querySelectorAll('.status-pill');
                    const paymentStatus = paymentStatusElements[0]?.textContent.trim();
                    const reconciledStatus = paymentStatusElements[1]?.textContent.trim();
                    
                    console.log(`Row ${index}:`, {
                        orderCodeDate,
                        createdAt,
                        paymentStatus,
                        reconciledStatus,
                        display: row.style.display || 'visible'
                    });
                }
            });
            
            // Check DataTable search functions
            console.log('DataTable search functions:', $.fn.dataTable.ext.search.length);
        };

        // Test specific filter
        window.testFilter = (filterType, filterValue) => {
            console.log(`🧪 Testing ${filterType} filter with value: ${filterValue}`);
            
            if (filterType === 'reconciled') {
                document.getElementById('reconciledFilter').value = filterValue;
                this.applyMainFilters();
                
                setTimeout(() => {
                    const visibleRows = $('#orderTable tbody tr:visible');
                    console.log(`Found ${visibleRows.length} visible rows`);
                    
                    visibleRows.each(function(index) {
                        if (index < 3) {
                            const reconciledStatus = $(this).find('.status-pill').eq(1).text().trim();
                            console.log(`Visible row ${index}: ${reconciledStatus}`);
                        }
                    });
                }, 200);
            } else if (filterType === 'payment') {
                document.getElementById('paymentFilter').value = filterValue;
                this.applyMainFilters();
                
                setTimeout(() => {
                    const visibleRows = $('#orderTable tbody tr:visible');
                    console.log(`Found ${visibleRows.length} visible rows`);
                    
                    visibleRows.each(function(index) {
                        if (index < 3) {
                            const paymentStatus = $(this).find('.status-pill').first().text().trim();
                            console.log(`Visible row ${index}: ${paymentStatus}`);
                        }
                    });
                }, 200);
            }
        };

        // Reset all filters
        window.resetAllFilters = () => {
            console.log('🔄 Resetting all filters...');
            
            // Clear main table filters
            $('#dateFilter').val('');
            $('#paymentFilter').val('');
            $('#reconciledFilter').val('');
            $('#customSearch').val('');
            $('#orderTable tbody tr').show();
            
            // Clear shop table filters
            $('[id^="dateFilter"]').not('#dateFilter').val('');
            $('[id^="paymentFilter"]').not('#paymentFilter').val('');
            $('[id^="reconciledFilter"]').not('#reconciledFilter').val('');
            $('[id^="customSearch"]').not('#customSearch').val('');
            $('[id^="orderTableSHOP"] tbody tr').show();
            
            // Hide all no-data messages
            $('#noDataMessage').hide();
            $('[id^="noDataMessageShop"]').hide();
            
            // Show all mobile cards
            $('.mobile-order-card').show();
            
            console.log('✅ All filters reset and all rows shown');
        };

        // Test mobile filter functionality
        window.testMobileFilter = () => {
            console.log('🧪 Testing mobile filter');
            if ($(window).width() <= 768) {
                this.showMobileFilter();
            } else {
                console.log('Not on mobile device');
            }
        };

        // Quick test function
        window.quickTest = () => {
            console.log('🚀 Quick filter test - Reconciled status');
            this.testFilter('reconciled', 'Đã đối soát');
            
            setTimeout(() => {
                console.log('--- Switching to "Chưa đối soát" ---');
                this.testFilter('reconciled', 'Chưa đối soát');
            }, 1500);
            
            setTimeout(() => {
                console.log('--- Testing payment filter ---');
                this.testFilter('payment', 'Đã thanh toán');
            }, 3000);
        };

        // Debug function to test modal functionality
        window.testModal = function() {
            console.log('🧪 Testing modal functionality...');
            
            // Check all view-order-btn buttons
            const buttons = document.querySelectorAll('.view-order-btn');
            console.log('📋 Found view-order-btn buttons:', buttons.length);
            
            buttons.forEach((button, index) => {
                if (index < 3) { // Only check first 3 buttons
                    const orderId = button.dataset.orderId;
                    const orderCode = button.dataset.orderCode;
                    const shopName = button.dataset.shopName;
                    const orderDetails = button.dataset.orderDetails;
                    
                    console.log(`Button ${index}:`, {
                        orderId,
                        orderCode,
                        shopName,
                        orderDetailsLength: orderDetails ? orderDetails.length : 0,
                        orderDetailsPreview: orderDetails ? orderDetails.substring(0, 100) + '...' : 'null'
                    });
                    
                    // Try to parse order details
                    if (orderDetails) {
                        try {
                            const parsed = JSON.parse(orderDetails);
                            console.log(`✅ Button ${index} - Parsed order details:`, parsed.length, 'items');
                        } catch (e) {
                            console.error(`❌ Button ${index} - Failed to parse order details:`, e.message);
                        }
                    }
                }
            });
            
            // Check modal elements
            const modal = document.getElementById('orderDetailModal');
            console.log('Modal element exists:', modal !== null);
            
            if (modal) {
                const orderCodeEl = modal.querySelector('#modal-order-code');
                const shopNameEl = modal.querySelector('#modal-shop-name');
                const orderDetailsEl = modal.querySelector('#modal-order-details');
                
                console.log('Modal elements found:', {
                    orderCode: orderCodeEl !== null,
                    shopName: shopNameEl !== null,
                    orderDetails: orderDetailsEl !== null
                });
            }
        };
        
        // Debug function to test clicking first button
        window.testModalClick = function() {
            console.log('🧪 Testing modal click...');
            
            const firstButton = document.querySelector('.view-order-btn');
            if (firstButton) {
                console.log('✅ Found first button, clicking...');
                firstButton.click();
            } else {
                console.log('❌ No view-order-btn found');
            }
        };
        
        // Debug function to test tab switching
        window.testTabSwitching = function() {
            console.log('🧪 Testing tab switching...');
            
            // Check all tab elements
            const tabs = document.querySelectorAll('a[data-bs-toggle="tab"]');
            console.log('📋 Found tabs:', tabs.length);
            
            tabs.forEach((tab, index) => {
                const href = tab.getAttribute('href');
                const id = tab.getAttribute('id');
                console.log(`Tab ${index}: id="${id}", href="${href}"`);
            });
            
            // Check all tab content
            const tabContents = document.querySelectorAll('.tab-pane');
            console.log('📋 Found tab contents:', tabContents.length);
            
            tabContents.forEach((content, index) => {
                const id = content.getAttribute('id');
                const isActive = content.classList.contains('active');
                console.log(`Content ${index}: id="${id}", active=${isActive}`);
            });
            
            // Check shop tables
            const shopTables = document.querySelectorAll('[id^="orderTableSHOP"]');
            console.log('📋 Found shop tables:', shopTables.length);
            
            shopTables.forEach((table, index) => {
                const id = table.getAttribute('id');
                const isVisible = table.style.display !== 'none';
                const parentVisible = table.closest('.tab-pane.active') !== null;
                console.log(`Shop table ${index}: id="${id}", visible=${isVisible}, parentActive=${parentVisible}`);
            });
        };
        
        // Debug function to manually switch to a shop tab
        window.switchToShopTab = function(shopId) {
            console.log(`🔄 Manually switching to shop tab: ${shopId}`);
            
            const tabLink = document.querySelector(`a[href="#shop-${shopId}-content"]`);
            const tabContent = document.querySelector(`#shop-${shopId}-content`);
            
            if (tabLink && tabContent) {
                console.log('✅ Found tab elements, triggering click...');
                tabLink.click();
                
                // Force show the table after a short delay
                setTimeout(() => {
                    const table = document.querySelector(`#orderTableSHOP${shopId}`);
                    if (table) {
                        table.style.display = 'table';
                        console.log(`✅ Forced table ${shopId} to be visible`);
                    }
                }, 100);
            } else {
                console.log('❌ Tab elements not found');
            }
        };
        
        // Debug function to show all shop tables
        window.showAllShopTables = function() {
            console.log('👁️ Showing all shop tables...');
            
            const shopTables = document.querySelectorAll('[id^="orderTableSHOP"]');
            shopTables.forEach((table) => {
                table.style.display = 'table';
                console.log(`✅ Made ${table.id} visible`);
            });
        };
        
        // Show debug functions info
        setTimeout(() => {
            console.log('💡 Available debug functions:');
            console.log('- window.debugFiltering() - Shows current filter states');
            console.log('- window.testFilter(type, value) - Test specific filters');
            console.log('  * testFilter("reconciled", "Đã đối soát")');
            console.log('  * testFilter("payment", "Đã thanh toán")');
            console.log('- window.resetAllFilters() - Reset all filters');
            console.log('- window.quickTest() - Run automated filter tests');
            console.log('- window.testMobileFilter() - Test mobile filter');
            console.log('- window.testModal() - Test modal functionality');
            console.log('- window.testModalClick() - Test clicking first modal button');
            console.log('- window.testTabSwitching() - Test tab switching');
            console.log('- window.switchToShopTab(shopId) - Manually switch to shop tab');
            console.log('- window.showAllShopTables() - Show all shop tables');
        }, 2000);
    }
}

// Shop-specific functions
class ShopOrderPage {
    constructor(shopId) {
        console.log(`🏪 Creating ShopOrderPage for shop ${shopId}`);
        this.shopId = shopId;
        this.shopTable = null;
        console.log(`🔍 ShopOrderPage constructor - shopId: ${this.shopId}`);
        this.init();
    }

    init() {
        console.log(`🔧 ShopOrderPage init() called for shop ${this.shopId}`);
        this.initDataTable();
        this.initFilters();
        console.log(`✅ ShopOrderPage init() completed for shop ${this.shopId}`);
    }

    initDataTable() {
        console.log(`📊 ShopOrderPage initDataTable() called for shop ${this.shopId}`);
        console.log(`📱 Window width: ${$(window).width()}`);
        
        if ($(window).width() > 768) {
            const tableSelector = `#orderTableSHOP${this.shopId}`;
            console.log(`🔍 Looking for table: ${tableSelector}`);
            console.log(`📊 Table element exists:`, $(tableSelector).length > 0);
            
            // Destroy existing DataTable if it exists
            if ($.fn.DataTable.isDataTable(tableSelector)) {
                console.log(`🔄 Destroying existing DataTable for shop ${this.shopId}`);
                $(tableSelector).DataTable().destroy();
            }
            
            // Initialize new DataTable
            const shopId = this.shopId; // Store shopId in a variable to use in callback
            this.shopTable = $(`#orderTableSHOP${this.shopId}`).DataTable({
                "paging": true,
                "searching": true,
                "ordering": false,
                "info": true,
                "lengthMenu": [10, 20, 50, 100, 150],
                "pageLength": 10,
                "dom": "t<'pagination-container'<'row'<'col-4'l><'col-4 text-center'i><'col-4'p>>>",
                "order": [[1, "desc"]],
                "language": {
                    "lengthMenu": "Hiển thị _MENU_ đơn hàng",
                    "zeroRecords": "Không tìm thấy dữ liệu",
                    "info": "Hiển thị _START_ đến _END_ của _TOTAL_ đơn hàng",
                    "infoEmpty": "Không có dữ liệu để hiển thị",
                    "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                    "search": "",
                    "paginate": {
                        "first": "Đầu",
                        "last": "Cuối",
                        "next": "Tiếp",
                        "previous": "Trước"
                    }
                },
                "initComplete": function() {
                    $(`#orderTableSHOP${shopId}`).show();
                    console.log(`✅ Shop DataTable ${shopId} initialized`);
                }
            });
            
            console.log(`✅ Shop DataTable ${this.shopId} created successfully`);
        } else {
            console.log(`📱 Skipping DataTable initialization for shop ${this.shopId} - mobile view`);
        }
        console.log(`📊 ShopOrderPage initDataTable() completed for shop ${this.shopId}`);
    }

    initFilters() {
        // Shop filter buttons
        $(`#applyFilters${this.shopId}`).on('click', () => {
            this.applyShopFilters();
        });

        $(`#clearFilters${this.shopId}`).on('click', () => {
            this.clearShopFilters();
        });

        $(`#customSearch${this.shopId}`).on('keyup', () => {
            this.applyShopFilters();
        });

        // Auto apply filters when selects change
        $(`#paymentFilter${this.shopId}, #reconciledFilter${this.shopId}`).on('change', () => {
            this.applyShopFilters();
        });

        $(`#dateFilter${this.shopId}`).on('change', () => {
            this.applyShopFilters();
        });
    }

    applyShopFilters() {
        const dateFilter = $(`#dateFilter${this.shopId}`).val();
        const paymentFilter = $(`#paymentFilter${this.shopId}`).val();
        const reconciledFilter = $(`#reconciledFilter${this.shopId}`).val();
        const searchFilter = $(`#customSearch${this.shopId}`).val().toLowerCase();
        
        console.log(`🔍 Applying filters for shop ${this.shopId}:`, {
            date: dateFilter,
            payment: paymentFilter,
            reconciled: reconciledFilter,
            search: searchFilter
        });

        $(`#orderTableSHOP${this.shopId} tbody tr`).each(function() {
            const $row = $(this);
            let show = true;

            // Date filter
            if (dateFilter) {
                const filterDate = $row.find('.order-code-date').text().trim();
                const createdAtText = $row.find('td').eq(1).find('small').text().trim();
                const createdAtDate = createdAtText.split(' ')[0];
                
                // Convert yyyy-mm-dd to d/m/Y
                const parts = dateFilter.split('-');
                const formattedDate = parts[2] + '/' + parts[1] + '/' + parts[0];
                
                if (filterDate !== formattedDate && createdAtDate !== formattedDate) {
                    show = false;
                }
            }

            // Payment status filter
            if (paymentFilter && show) {
                const paymentStatus = $row.find('.status-pill').first().text().trim();
                if (paymentStatus !== paymentFilter) {
                    show = false;
                }
            }

            // Reconciled filter
            if (reconciledFilter && show) {
                const statusPills = $row.find('.status-pill');
                let reconciledStatus = '';
                if (statusPills.length > 1) {
                    reconciledStatus = statusPills.eq(1).text().trim();
                }
                if (reconciledStatus !== reconciledFilter) {
                    show = false;
                }
            }

            // Search filter
            if (searchFilter && show) {
                const orderCode = $row.find('.order-link').first().text().trim().toLowerCase();
                if (!orderCode.includes(searchFilter)) {
                    show = false;
                }
            }

            if (show) {
                $row.show();
            } else {
                $row.hide();
            }
        });
        
        // Update pagination info and show/hide no data message
        const visibleRows = $(`#orderTableSHOP${this.shopId} tbody tr:visible`).length;
        const totalRows = $(`#orderTableSHOP${this.shopId} tbody tr`).length;
        console.log(`✅ Shop ${this.shopId}: Showing ${visibleRows} of ${totalRows} rows after filtering`);
        
        // Update DataTable pagination info manually for shop table
        if (this.shopTable) {
            const info = this.shopTable.page.info();
            const start = info.start + 1;
            const end = Math.min(info.start + visibleRows, info.recordsTotal);
            
            // Update the info text
            $(`#orderTableSHOP${this.shopId}_wrapper .dataTables_info`).html(
                `Hiển thị ${start} đến ${end} của ${visibleRows} đơn hàng`
            );
            
            // Update pagination controls
            if (visibleRows <= info.length) {
                $('.dataTables_paginate').hide();
            } else {
                $('.dataTables_paginate').show();
            }
        }
        
        // Show/hide no data message
        if (visibleRows === 0) {
            $('#noDataMessage').show();
        } else {
            $('#noDataMessage').hide();
        }
    }

    clearShopFilters() {
        console.log('🧹 Clearing shop filters...');
        $(`#dateFilter${this.shopId}`).val('');
        $(`#paymentFilter${this.shopId}`).val('');
        $(`#reconciledFilter${this.shopId}`).val('');
        $(`#customSearch${this.shopId}`).val('');
        $(`#orderTableSHOP${this.shopId} tbody tr`).show();
        $('#noDataMessage').hide();
        $('.mobile-order-card').show();
        
        // Reset DataTable pagination info
        if (this.shopTable) {
            const info = this.shopTable.page.info();
            const totalRows = $(`#orderTableSHOP${this.shopId} tbody tr`).length;
            const start = info.start + 1;
            const end = Math.min(info.start + info.length, totalRows);
            
            // Update the info text back to original
            $(`#orderTableSHOP${this.shopId}_wrapper .dataTables_info`).html(
                `Hiển thị ${start} đến ${end} của ${totalRows} đơn hàng`
            );
            
            // Show pagination controls
            $('.dataTables_paginate').show();
        }
    }
}

// Initialize OrderPage when document is ready
$(document).ready(function() {
    console.log('🚀 Document ready, initializing OrderPage...');
    
    // Initialize main OrderPage
    window.orderPage = new OrderPage();
    console.log('✅ Main OrderPage instance created:', window.orderPage);
    
    // Initialize shop-specific pages if they exist
    $('[id^="orderTableSHOP"]').each(function() {
        const shopId = $(this).attr('id').replace('orderTableSHOP', '');
        console.log(`🔍 Found shop table: ${shopId}`);
        if (shopId && !window[`shopOrderPage${shopId}`]) {
            window[`shopOrderPage${shopId}`] = new ShopOrderPage(shopId);
            console.log(`✅ ShopOrderPage ${shopId} initialized`);
        } else if (shopId) {
            console.log(`⚠️ ShopOrderPage ${shopId} already exists, skipping`);
        }
    });
    
    // Add tab switching event handler for shop tabs
    $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
        const target = $(e.target).attr("href");
        console.log('🔄 Tab switched to:', target);
        
        // Check if this is a shop tab
        if (target.startsWith('#shop-') && target.endsWith('-content')) {
            const shopId = target.replace('#shop-', '').replace('-content', '');
            console.log(`🏪 Shop tab activated: ${shopId}`);
            
            // Initialize DataTable for this shop if it doesn't exist
            const tableId = `#orderTableSHOP${shopId}`;
            console.log(`🔍 Looking for table: ${tableId}`);
            console.log(`📊 Table exists:`, $(tableId).length > 0);
            console.log(`📊 DataTable initialized:`, $.fn.DataTable.isDataTable(tableId));
            
            if (!$.fn.DataTable.isDataTable(tableId)) {
                console.log(`📊 Initializing DataTable for shop ${shopId}`);
                if (!window[`shopOrderPage${shopId}`]) {
                    window[`shopOrderPage${shopId}`] = new ShopOrderPage(shopId);
                    console.log(`✅ ShopOrderPage ${shopId} initialized on tab switch`);
                }
            } else {
                console.log(`📊 DataTable for shop ${shopId} already exists`);
            }
            
            // Show the table immediately
            $(tableId).show();
            console.log(`👁️ Table ${tableId} should now be visible`);
            
            // Force show the table container as well
            $(tableId).closest('.table-wrapper').show();
            $(tableId).closest('.modern-table-container').show();
            
            // If DataTable is not initialized yet, initialize it
            if (!$.fn.DataTable.isDataTable(tableId)) {
                console.log(`📊 Initializing DataTable for shop ${shopId} on tab switch`);
                if (!window[`shopOrderPage${shopId}`]) {
                    window[`shopOrderPage${shopId}`] = new ShopOrderPage(shopId);
                }
            }
        }
    });
    
    console.log('✅ All OrderPage instances initialized');
    
    // Test modal functionality
    setTimeout(() => {
        console.log('🧪 Testing modal functionality...');
        const modalElement = $('#orderDetailModal');
        console.log('Modal element exists:', modalElement.length > 0);
        
        if (modalElement.length > 0) {
            console.log('Modal HTML:', modalElement.html().substring(0, 200) + '...');
        }
        
        // Test button click
        const testButton = $('.view-order-btn').first();
        console.log('Test button exists:', testButton.length > 0);
        if (testButton.length > 0) {
            console.log('Test button data:', {
                orderId: testButton.data('order-id'),
                orderCode: testButton.data('order-code')
            });
        }
    }, 1000);

    // Khi click vào card mobile sẽ hiện modal chi tiết (nếu không phải click vào nút chi tiết)
    $(document).on('click', '.mobile-order-card', function(e) {
        if ($(e.target).closest('.view-order-btn').length) return;
        $(this).find('.view-order-btn').trigger('click');
    });
});