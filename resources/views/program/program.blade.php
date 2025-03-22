@extends('layout')
@section('title', 'Tạo gói đăng sản phẩm')
@section('main')
<div class="container-fluid mt-1 bg-white p-5 border-radius-10px">
    <form id="programForm" method="POST" action="{{ route('program.store') }}">
        @csrf
        <div class="row">
            <div class="col-12">
                <div class="col-3 mb-1">
                    <label for="package_name" class="form-label text-info"> Đặt tên gói</label>
                    <input type="text" class="form-control text-danger" id="package_name" name="name_program" maxlength="30" required placeholder="Nhập tên gói (tối đa 30 ký tự)">
                </div>
                <div class="mb-3">
                    <div class="d-flex flex-wrap align-items-center">
                        <div class="form-check me-3 mb-3">
                            <input class="form-check-input" type="checkbox" id="select_all_shops" onclick="toggleSelectAll(this)">
                            <label class="form-check-label" for="select_all_shops">All shop</label>
                        </div>
                        @foreach($shops as $shop)
                        <div class="form-check me-3 mb-3">
                            <input class="form-check-input shop-checkbox" type="checkbox" name="shop_list[]" value="{{ $shop->shop_id }}">
                            <label class="form-check-label">{{ $shop->shop_name }}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div>
            <div class="table-responsive mb-3 mt-2">
                <table class="table table-bordered" id="product_table">
                    <thead>
                        <tr>
                            <th class="col-2">SKU</th>
                            <th class="col-3">Tên sản phẩm</th>
                            <th class="col-2">Hình ảnh</th>
                            <th class="col-1">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <input type="text" class="form-control text-uppercase" name="sku[]" required
                                    onkeypress="handleSKUEvent(event)"
                                    onblur="handleSKUEvent(event)"
                                    oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_.]/g, '')">
                            </td>
                            <td>
                                <input type="hidden" name="name[]" value="">
                                <a class="product-name"></a>
                            </td>
                            <td>
                                <input type="hidden" name="image[]" value="">
                                <img src="" class="product-image" style="width:50px; height:50px; display:none;">
                            </td>
                            <td>
                                <button type="button" class="btn btn-danger" onclick="removeRow(this)">Xóa</button>
                            </td>
                        </tr>
                    </tbody>

                </table>
            </div>
            <div class="mb-3">
                <label for="package_description" class="form-label">Mô tả gói</label>
                <textarea class="form-control" id="package_description" name="description" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Tạo gói đăng sản phẩm</button>
        </div>
    </form>
</div>

<script>
    const productCache = {}; // Lưu cache sản phẩm

    async function fetchProductBySKU(sku, callback) {
        if (productCache[sku]) {
            callback(productCache[sku]);
            return;
        }

        try {
            const response = await fetch(`/get-product/${encodeURIComponent(sku.trim())}`);
            const data = await response.json();
            if (!data.error) {
                productCache[sku] = data;
            }
            callback(data);
        } catch (err) {
            console.error('Lỗi khi lấy dữ liệu sản phẩm:', err);
        }
    }

    function handleSKUEvent(event) {
        if (event.type === "keypress" && event.key !== "Enter") return;

        const input = event.target;
        input.value = input.value.toUpperCase().replace(/[^A-Z0-9_.]/g, '');
        const sku = input.value.trim();
        const row = input.closest('tr');

        if (sku.length === 0) return;

        fetchProductBySKU(sku, function(product) {
            if (product.error) {
                row.querySelector('.product-name').innerText = "Không tìm thấy";
                row.querySelector('.product-image').src = "";
                row.querySelector('.product-image').style.display = "none";

                row.querySelector('input[name="name[]"]').value = "";
                row.querySelector('input[name="image[]"]').value = "";
                return;
            }

            // Gán dữ liệu sản phẩm vào input ẩn & hiển thị lên giao diện
            row.querySelector('.product-name').innerText = product.name;
            row.querySelector('.product-image').src = product.image;
            row.querySelector('.product-image').style.display = "block";

            row.querySelector('input[name="name[]"]').value = product.name;
            row.querySelector('input[name="image[]"]').value = product.image;

            addNewRowIfNeeded();
        });
    }

    function addNewRowIfNeeded() {
        const table = document.getElementById('product_table').getElementsByTagName('tbody')[0];
        const lastRow = table.rows[table.rows.length - 1];
        const lastSKUInput = lastRow ? lastRow.cells[0].querySelector('input[name="sku[]"]') : null;

        if (lastSKUInput && lastSKUInput.value.trim() !== '') {
            addNewRow();
        }
    }

    function addNewRow() {
        const table = document.getElementById('product_table').getElementsByTagName('tbody')[0];
        const newRow = table.insertRow();
        newRow.innerHTML = `
            <td>
                <input type="text" class="form-control text-uppercase" name="sku[]" required 
                    onkeypress="handleSKUEvent(event)" 
                    onblur="handleSKUEvent(event)" 
                    oninput="this.value = this.value.toUpperCase().replace(/[^A-Z0-9_.]/g, '')">
            </td>
            <td>
                <input type="hidden" name="name[]" value="">
                <a class="product-name"></a>
            </td>
            <td>
                <input type="hidden" name="image[]" value="">
                <img src="" class="product-image" style="width:50px; height:50px; display:none;">
            </td>
            <td>
                <button type="button" class="btn btn-danger" onclick="removeRow(this)">Xóa</button>
            </td>
        `;
    }

    function removeRow(button) {
        const row = button.closest('tr');
        const table = document.getElementById('product_table').getElementsByTagName('tbody')[0];
        if (table.rows.length > 1) {
            table.deleteRow(row.rowIndex - 1);
        }
    }

    function toggleSelectAll(source) {
        document.querySelectorAll('.shop-checkbox').forEach(checkbox => checkbox.checked = source.checked);
    }

    function validateForm() {
        if (!document.querySelector('.shop-checkbox:checked')) {
            alert('Vui lòng chọn ít nhất một shop.');
            return false;
        }
        return true;
    }
</script>
@endsection
