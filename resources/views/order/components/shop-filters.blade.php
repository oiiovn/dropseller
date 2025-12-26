{{-- Component: Shop Filters --}}
<div class="filter-section" id="filtersSection{{$shop->shop_id}}">
    <div class="row align-items-end">
        <div class="col-md-3">
            <input type="date" class="form-control" id="dateFilter{{$shop->shop_id}}" placeholder="Chọn ngày">
        </div>
        <div class="col-md-2">
            <select class="form-select" id="paymentFilter{{$shop->shop_id}}" style="color: #666;">
                <option value="" style="color: #666;">Thanh toán</option>
                <option value="Đã thanh toán">Đã thanh toán</option>
                <option value="Chưa thanh toán">Chưa thanh toán</option>
            </select>
        </div>
        <div class="col-md-2">
            <select class="form-select" id="reconciledFilter{{$shop->shop_id}}" style="color: #666;">
                <option value="" style="color: #666;">Đối soát</option>
                <option value="Đã đối soát">Đã đối soát</option>
                <option value="Chưa đối soát">Chưa đối soát</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" class="form-control" id="customSearch{{$shop->shop_id}}" placeholder="Tìm theo mã đơn...">
        </div>
        <div class="col-md-2">
            <div class="d-flex gap-2">
                <button type="button" class="btn btn-primary" id="applyFilters{{$shop->shop_id}}">
                    <i class="ri-search-line me-1"></i>Lọc
                </button>
                <button type="button" class="btn btn-secondary" id="clearFilters{{$shop->shop_id}}">
                    <i class="ri-refresh-line me-1"></i>Reset
                </button>
            </div>
        </div>
    </div>
</div> 
<script>
  const selectEl = document.getElementById('reconciledFilter{{$shop->shop_id}}');
  const selectEl2 = document.getElementById('paymentFilter{{$shop->shop_id}}');
  function updateSelectColor() {
    if (selectEl.value === "") {
      selectEl.style.color = '#666'; // màu xám khi chọn "Đối soát"
    } else {
      selectEl.style.color = '#000'; // màu xanh khi chọn giá trị khác
    }
  }
  function updateSelectColor2() {
    if (selectEl2.value === "") {
      selectEl2.style.color = '#666'; // màu xám khi chọn "Đối soát"
    } else {
      selectEl2.style.color = '#000'; // màu xanh khi chọn giá trị khác
    }
  }
  // Gọi ngay khi load trang
  updateSelectColor();
  updateSelectColor2();
  // Gọi khi có thay đổi
  selectEl.addEventListener('change', updateSelectColor);
  selectEl2.addEventListener('change', updateSelectColor2);
</script>