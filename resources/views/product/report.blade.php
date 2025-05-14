@extends('layout')
@section('title', 'main')
@section('main')
<div class="container-fluid mb-1 bg-white">
    <div class="row " style="display: flex; align-items: center; justify-content: space-between; height:70px">
        <!-- Form bên trái -->
        <div class="col-9" style="flex: 1;">
            <form action="{{ route('product.report') }}" method="post" style="display: flex; align-items: center; gap: 10px;">
                @csrf
                <select name="platform" class="border-light text-body-emphasis" style="padding: 5px; border-radius: 5px; border: 1px solid #BEBEBE;">
                    <option value="Tiktok">Tiktok</option>
                    <option value="Shopee">Shopee</option>
                    <option value="Lazada">Lazada</option>
                </select>
                <select name="shop_id" id="shop_id" class="border-light text-body-emphasis" style="padding: 5px; border-radius: 5px; border: 1px solid #BEBEBE;">
                    <option value="">Chọn Shop</option>
                    @if (!empty($shop_get) && count($shop_get) > 0)
                    @foreach($shop_get as $shop)
                    <option
                        value="{{ $shop['shop_id'] ?? $shop->shop_id }}"
                        {{ (isset($shopId) && $shopId == ($shop['shop_id'] ?? $shop->shop_id)) ? 'selected' : '' }}>
                        {{ $shop['shop_name'] ?? $shop->shop_name }}
                    </option>
                    @endforeach
                    @else
                    <option value="">Không có shop nào</option>
                    @endif
                </select>


                <input
                    type="date"
                    name="start_date"
                    class="border-light text-body-emphasis"
                    style="padding: 5px; border-radius: 5px; border: 1px solid #BEBEBE;"
                    value="{{ $startDate ?? '' }}"
                    placeholder="Từ ngày"
                    required>

                <input
                    type="date"
                    name="end_date"
                    class="border-light text-body-emphasis"
                    style="padding: 5px; border-radius: 5px; border: 1px solid #BEBEBE;"
                    value="{{ $endDate ?? '' }}"
                    placeholder="Đến ngày"
                    required>


                <button type="submit" class="btn btn-secondary waves-effect waves-light" style="padding: 5px; border-radius: 5px; border: 1px solid #BEBEBE;">Lọc dữ liệu</button>
            </form>
        </div>
        <!-- Nút xuất hóa đơn bên phải -->
        <div class="col-3" style="display: flex; justify-content: flex-end;">
            @if (!empty($filteredProducts) && count($filteredProducts) > 0 && !empty($filterDate) && !empty($shopId))
            <form action="{{ route('order.im') }}" method="POST">
                @csrf
                <input type="hidden" name="data" value="{{ json_encode($filteredProducts) }}">
                <input type="hidden" name="filterDate" value="{{ $filterDate }}">
                <input type="hidden" name="shop_id" value="{{ $shopId }}">
                <button type="submit" class="btn btn-primary" style="padding: 5px; border-radius: 5px; border: 1px solid #BEBEBE;">Xuất hóa đơn</button>
            </form>
            @endif

        </div>
    </div>
    <div class="row " style="display: flex; align-items: center; justify-content: space-between; height:70px">
        <!-- Form bên trái -->
        <div class="col-9" style="flex: 1;">
            <form action="{{ route('get_shop') }}" method="post" style="display: flex; align-items: center; gap: 10px;">
                @csrf
                <select name="platform" class="border-light text-body-emphasis" style="padding: 5px; border-radius: 5px; border: 1px solid #BEBEBE;">
                    <option value="Tiktok">Tiktok</option>
                    <option value="Shopee">Shopee</option>
                    <option value="Lazada">Lazada</option>
                </select>

                <input
                    type="date"
                    name="start_date"
                    class="border-light text-body-emphasis"
                    style="padding: 5px; border-radius: 5px; border: 1px solid #BEBEBE;"
                    value="{{ $startDate ?? '' }}"
                    placeholder="Từ ngày"
                    required>

                <input
                    type="date"
                    name="end_date"
                    class="border-light text-body-emphasis"
                    style="padding: 5px; border-radius: 5px; border: 1px solid #BEBEBE;"
                    value="{{ $endDate ?? '' }}"
                    placeholder="Đến ngày"
                    required>


                <button type="submit" class="btn btn-secondary waves-effect waves-light" style="padding: 5px; border-radius: 5px; border: 1px solid #BEBEBE;">Lọc Shop ID</button>
            </form>
        </div>

    </div>

    @if (!empty($filteredProducts))
    <div style=" overflow-x: auto; max-height: 750px; overflow-y: auto; position: relative;">
        <table class="table table-hover table-nowrap mb-0 bg-white">
            <thead style="position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">
                <tr class="bg-light">
                    <th scope="col">Mã sản phẩm</th>
                    <th scope="col">Tên sản phẩm</th>
                    <th scope="col">Lượt bán ({{$totalAmounts}})</th>
                    <th scope="col">Giá sỉ</th>
                    <th scope="col">Tổng giá</th>
                    <th scope="col" style="max-width: 50px;">Hình ảnh</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($filteredProducts as $product)
                <tr style="vertical-align: middle;">
                    <td>{{ $product['code'] ?? 'Không rõ' }}</td>
                    <td>{{ $product['name'] ?? 'Không rõ' }}</td>
                    <td style="text-align: center;">{{ $product['amount'] ?? 0 }}</td>
                    <td>{{ number_format($product['db_price'] ?? 0) }} VNĐ</td>
                    <td>{{ number_format($product['db_price'] * $product['amount'] ) }} VNĐ</td>
                    <td>
                        @if (!empty($product['image']))
                        <img src="{{ $product['image'] }}" alt="Hình ảnh" style="width: 50px; height: auto; border-radius: 10px;">
                        @else
                        Không có hình ảnh
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @elseif(!empty($Shop_id))
    <div style=" overflow-x: auto; max-height: 750px; overflow-y: auto; position: relative;">
        <table class="table table-hover table-nowrap mb-0 bg-white">
            <thead style="position: sticky; top: 0; z-index: 1; background-color: #f8f9fa;">
                <tr class="bg-light">
                    <th scope="col">ID SHOP</th>
                    <th scope="col">Tên Shop</th>

                </tr>
            </thead>
            <tbody>
                @foreach ($Shop_id as $shop_id)
                <tr style="vertical-align: middle;">
                    <td>
                        <li style="list-style: none; padding: 0; margin: 0;" class="hienthicopy">
                            <a class="fw-medium link-primary order-link text-secondary" data-order-code="{{$shop_id}}">
                            {{ $shop_id }}
                                <span class="ri-checkbox-multiple-blank-line icon"></span>
                            </a>
                        </li>
                    </td>
                    <td>
                        @php
                        $shopName = 'Shop Mới';
                        if (!empty($shop_get) && count($shop_get) > 0) {
                        foreach ($shop_get as $shop) {
                        if ($shop['shop_id'] == $shop_id) {
                        $shopName = $shop['shop_name'];
                        break;
                        }
                        }
                        }
                        @endphp
                        {{ $shopName }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @else
    <p>Không tìm thấy nội dung nào phù hợp với bộ lọc.</p>
    @endif

</div>
@endsection