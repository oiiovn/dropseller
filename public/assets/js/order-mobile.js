// JS cho filter và phân trang mobile order page

window.initOrderMobile = function() {
    // Tránh bind trùng lặp
    if (window.__ORDER_MOBILE_INIT__) {
        // Xóa event cũ trước khi bind lại
        document.getElementById('mobilePrevPage')?.removeEventListener('click', window.__ORDER_MOBILE_PREV__);
        document.getElementById('mobileNextPage')?.removeEventListener('click', window.__ORDER_MOBILE_NEXT__);
        document.getElementById('mobileDateInput')?.removeEventListener('change', window.__ORDER_MOBILE_DATE__);
        document.getElementById('mobilePaymentFilter')?.removeEventListener('change', window.__ORDER_MOBILE_PAY__);
        document.getElementById('mobileReconciledFilter')?.removeEventListener('change', window.__ORDER_MOBILE_REC__);
        document.getElementById('mobileShopFilter')?.removeEventListener('change', window.__ORDER_MOBILE_SHOP__);
        document.getElementById('mobileSearchInput')?.removeEventListener('keyup', window.__ORDER_MOBILE_SEARCH__);
    }
    window.__ORDER_MOBILE_INIT__ = true;

    const filterBar = document.getElementById('mobile-filter-bar');
    const searchBar = document.getElementById('mobile-search-bar');
    const showSearchBtn = document.getElementById('mobileShowSearchBar');
    const closeSearchBtn = document.getElementById('mobileCloseSearchBar');
    let currentPage = 1;
    const itemsPerPage = 10;
    let filteredCards = [];
    let totalPages = 1;

    function formatDisplayDate(filterDate) {
        if (filterDate && filterDate.includes(' - ')) {
            return filterDate.split(' - ')[0];
        }
        return filterDate;
    }

    function applyMobileFilters() {
        const dateFilter = document.getElementById('mobileDateInput').value;
        const paymentFilter = document.getElementById('mobilePaymentFilter').value;
        const reconciledFilter = document.getElementById('mobileReconciledFilter').value;
        const searchFilter = document.getElementById('mobileSearchInput').value.toLowerCase();
        const shopFilter = document.getElementById('mobileShopFilter').value;
        const allCards = Array.from(document.querySelectorAll('.mobile-order-card'));
        const visibleCards = allCards.filter(card => {
            const cardContainer = card.closest('.tab-pane');
            return !cardContainer || cardContainer.classList.contains('active');
        });
        visibleCards.forEach(function(card) {
            card.style.display = 'none';
        });
        filteredCards = [];
        visibleCards.forEach(function(card) {
            let show = true;
            if (dateFilter) {
                const cardFilterDate = card.dataset.filterDate;
                const cardCreatedAt = card.dataset.createdAt;
                const createdAtDate = cardCreatedAt.split(' ')[0];
                const parts = dateFilter.split('-');
                const formattedDate = parts[2] + '/' + parts[1] + '/' + parts[0];
                const displayDate = formatDisplayDate(cardFilterDate);
                if (cardFilterDate !== formattedDate && createdAtDate !== formattedDate && displayDate !== formattedDate) {
                    show = false;
                }
            }
            if (paymentFilter && show) {
                const cardPaymentStatus = card.dataset.paymentStatus;
                if (cardPaymentStatus !== paymentFilter) {
                    show = false;
                }
            }
            if (reconciledFilter && show) {
                const cardReconciledStatus = card.dataset.reconciled;
                if (cardReconciledStatus !== reconciledFilter) {
                    show = false;
                }
            }
            if (searchFilter && show) {
                const cardOrderCode = card.dataset.orderCode.toLowerCase();
                const cardShopName = card.dataset.shopName.toLowerCase();
                if (!cardOrderCode.includes(searchFilter) && !cardShopName.includes(searchFilter)) {
                    show = false;
                }
            }
            if (shopFilter && show) {
                const cardShopName = card.dataset.shopName;
                if (cardShopName !== shopFilter) {
                    show = false;
                }
            }
            if (show) {
                filteredCards.push(card);
            }
        });
        currentPage = 1;
        updateMobilePagination();
        const noDataMessage = document.getElementById('mobileNoDataMessage');
        if (filteredCards.length === 0) {
            noDataMessage.style.display = 'block';
            document.querySelector('.mobile-pagination-container').style.display = 'none';
        } else {
            noDataMessage.style.display = 'none';
            document.querySelector('.mobile-pagination-container').style.display = 'flex';
        }
    }

    function updateMobilePagination() {
        totalPages = Math.ceil(filteredCards.length / itemsPerPage);
        document.getElementById('mobileTotalPages').textContent = totalPages;
        document.getElementById('mobileCurrentPage').textContent = currentPage;
        const startIndex = (currentPage - 1) * itemsPerPage + 1;
        const endIndex = Math.min(currentPage * itemsPerPage, filteredCards.length);
        document.getElementById('mobilePrevPage').disabled = currentPage === 1;
        document.getElementById('mobileNextPage').disabled = currentPage === totalPages;
        filteredCards.forEach((card, index) => {
            const shouldShow = index >= (currentPage - 1) * itemsPerPage && index < currentPage * itemsPerPage;
            card.style.display = shouldShow ? 'block' : 'none';
        });
    }

    // Lưu lại các handler để có thể remove khi re-init
    window.__ORDER_MOBILE_PREV__ = function() {
        if (currentPage > 1) {
            currentPage--;
            updateMobilePagination();
        }
    };
    window.__ORDER_MOBILE_NEXT__ = function() {
        if (currentPage < totalPages) {
            currentPage++;
            updateMobilePagination();
        }
    };
    window.__ORDER_MOBILE_DATE__ = applyMobileFilters;
    window.__ORDER_MOBILE_PAY__ = applyMobileFilters;
    window.__ORDER_MOBILE_REC__ = applyMobileFilters;
    window.__ORDER_MOBILE_SHOP__ = applyMobileFilters;
    window.__ORDER_MOBILE_SEARCH__ = applyMobileFilters;

    document.getElementById('mobilePrevPage')?.addEventListener('click', window.__ORDER_MOBILE_PREV__);
    document.getElementById('mobileNextPage')?.addEventListener('click', window.__ORDER_MOBILE_NEXT__);
    document.getElementById('mobileDateInput')?.addEventListener('change', window.__ORDER_MOBILE_DATE__);
    document.getElementById('mobilePaymentFilter')?.addEventListener('change', window.__ORDER_MOBILE_PAY__);
    document.getElementById('mobileReconciledFilter')?.addEventListener('change', window.__ORDER_MOBILE_REC__);
    document.getElementById('mobileShopFilter')?.addEventListener('change', window.__ORDER_MOBILE_SHOP__);
    document.getElementById('mobileSearchInput')?.addEventListener('keyup', window.__ORDER_MOBILE_SEARCH__);

    window.clearMobileFilters = function() {
        document.getElementById('mobileDateInput').value = '';
        document.getElementById('mobilePaymentFilter').value = '';
        document.getElementById('mobileReconciledFilter').value = '';
        document.getElementById('mobileSearchInput').value = '';
        document.getElementById('mobileShopFilter').value = '';
        currentPage = 1;
        const visibleCards = Array.from(document.querySelectorAll('.mobile-order-card')).filter(card => {
            const cardContainer = card.closest('.tab-pane');
            return !cardContainer || cardContainer.classList.contains('active');
        });
        visibleCards.forEach(function(card) {
            card.style.display = 'block';
        });
        filteredCards = visibleCards;
        updateMobilePagination();
        document.getElementById('mobileNoDataMessage').style.display = 'none';
        document.querySelector('.mobile-pagination-container').style.display = 'flex';
    };
    showSearchBtn && showSearchBtn.addEventListener('click', function() {
        filterBar.classList.add('d-none');
        searchBar.classList.remove('d-none');
        setTimeout(()=>{searchBar.querySelector('input').focus();}, 200);
    });
    closeSearchBtn && closeSearchBtn.addEventListener('click', function() {
        searchBar.classList.add('d-none');
        filterBar.classList.remove('d-none');
    });
    setTimeout(applyMobileFilters, 100);
};
// Tự động chạy khi load lần đầu (nếu có mobile filter)
if (document.getElementById('mobile-filter-bar')) {
    window.initOrderMobile();
}
