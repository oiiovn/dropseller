@extends('layout')

@section('title', 'Danh sách chương trình')

@section('main')
<style>
    .form-check {
        display: flex;
        align-items: center;
        gap: 5px;
    }
</style>

<div class="container-fluid bg-white p-4">
    <h2 class="mb-4">Danh sách chương trình</h2>

    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="programTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="tab-all" data-bs-toggle="tab" data-bs-target="#tabAllPrograms" type="button" role="tab">
                Tất cả chương trình
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="tab-registered" data-bs-toggle="tab" data-bs-target="#tabRegisteredPrograms" type="button" role="tab">
                Chương trình đã đăng ký
            </button>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content" id="programTabContent">
        <!-- Tất cả chương trình -->
        <div class="tab-pane fade show active" id="tabAllPrograms" role="tabpanel">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Mã </th>
                        <th>Tên chương trình</th>
                        <th>Mô tả</th>
                        <th>Shop cần lên</th>
                        <th>Số lượng </th>
                        <th>Giá Sản phẩm (1 sản phẩm)</th>
                        <th>Tổng tiền</th>
                        <th>Xem chi tiết</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($programs as $program)
                    @php
                    $products = json_decode($program->products, true);
                    $productCount = is_array($products) ? count($products) : 0;
                    $price_per_product = 2000;
                    @endphp
                    <tr>
                        <td style="text-align: center;">{{ $program->id }}</td>
                        <td style="text-align: center;  width:12%">{{ $program->name_program }}</td>
                        <td style="text-align: center; width:15%">{{ $program->description }}</td>
                        <td style="text-align: center;">
                            <form method="POST" action="{{ route('program.shop.register') }}">
                                @csrf
                                <input type="hidden" name="program_id" value="{{ $program->id }}">

                                @if(isset($programShops[$program->id]) && count($programShops[$program->id]) > 1)
                                <div class="d-flex flex-column align-items-start shop-checkboxes"
                                    data-program-id="{{ $program->id }}"
                                    data-price="{{ $price_per_product }}"
                                    data-product-count="{{ $productCount }}">
                                    @foreach($programShops[$program->id] as $shop)
                                    <div class="form-check">
                                        <input class="form-check-input"
                                            type="checkbox"
                                            name="selected_shops[]"
                                            value="{{ $shop['shop_id'] }}"
                                            {{ $shop['is_registered'] ? 'checked disabled' : '' }}
                                            onchange="updateTotalPayment({{ $program->id }})">
                                        <label class="form-check-label">
                                            {{ $shop['shop_name'] }}
                                            @if($shop['is_registered']) <span class="text-success">(Đã đăng ký)</span> @endif
                                        </label>
                                    </div>
                                    @endforeach
                                </div>
                                @elseif(isset($programShops[$program->id]) && count($programShops[$program->id]) === 1)
                                <div class="d-flex flex-column align-items-start">
                                    <input type="hidden" name="selected_shops[]" value="{{ $programShops[$program->id][0]['shop_id'] }}">
                                    {{ $programShops[$program->id][0]['shop_name'] }}
                                </div>
                                @else
                                Không có shop
                                @endif
                        </td>
                        <td style="text-align: center; width:7%">{{ $productCount }}</td>
                        <td style="text-align: center;  width:16%">{{ number_format($price_per_product) }}VNĐ</td>
                        <td style="text-align: center;">
                            <span id="total-payment-{{ $program->id }}">
                                {{ number_format($price_per_product * $productCount) }}VNĐ
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <a type="button" data-bs-toggle="modal" data-bs-target="#exampleModal{{$program->id}}">
                                <li class="list-inline-item" title="Xem chi tiết">
                                    <i class="ri-eye-fill fs-16 text-primary"></i>
                                </li>
                            </a>

                            <!-- Modal chi tiết -->
                            <div class="modal fade" id="exampleModal{{$program->id}}" tabindex="-1" aria-labelledby="exampleModalLabel{{$program->id}}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel{{$program->id}}">Danh sách sản phẩm</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                        </div>
                                        <div class="modal-body">
                                            @if($productCount > 0)
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>SKU</th>
                                                        <th>Tên sản phẩm</th>
                                                        <th>Hình ảnh</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($products as $product)
                                                    <tr>
                                                        <td>{{ $product['sku'] ?? 'N/A' }}</td>
                                                        <td>{{ $product['name'] ?? 'Không có tên' }}</td>
                                                        <td>
                                                            @if(!empty($product['image']))
                                                            <img src="{{ $product['image'] }}" alt="Hình ảnh" style="width: 50px; height: 50px;">
                                                            @else
                                                            Không có ảnh
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <p><strong>Tổng số sản phẩm:</strong> {{ $productCount }}</p>
                                            @else
                                            <p>Không có sản phẩm</p>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <button type="button" class="btn btn-primary mt-2" data-bs-toggle="modal" data-bs-target="#confirmModal{{ $program->id }}">
                                Đăng ký ngay
                            </button>

                            <!-- Modal xác nhận -->
                            <div class="modal fade" id="confirmModal{{ $program->id }}" tabindex="-1" aria-labelledby="confirmModalLabel{{ $program->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title">Xác nhận đăng ký</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                        </div>
                                        <div class="modal-body">
                                            Bạn có chắc chắn muốn đăng ký chương trình này không?
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-primary">Xác nhận</button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            </form>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>


        <!-- Chương trình đã đăng ký -->
        <!-- Chương trình đã đăng ký -->
        <div class="tab-pane fade" id="tabRegisteredPrograms" role="tabpanel">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Mã </th>
                        <th>Tên chương trình</th>
                        <th>Mô tả</th>
                        <th>Shop cần lên</th>
                        <th>Số lượng sản phẩm</th>
                        <th>Giá Sản phẩm (1 sản phẩm)</th>
                        <th>Tổng tiền</th>
                        <th>Xem chi tiết</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($registeredPrograms as $program)
                    <tr>
                        <td>{{ $program->id }}</td>
                        <td>{{ $program->name_program }}</td>
                        <td>{{ $program->description }}</td>
                        <td>
                            @if(isset($registeredProgramShops[$program->id]) && count($registeredProgramShops[$program->id]) > 0)
                            <div class="d-flex flex-column align-items-start">
                                @foreach($registeredProgramShops[$program->id] as $shop)
                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        disabled
                                        {{ $shop['is_registered'] ? 'checked' : '' }}>
                                    <label class="form-check-label">
                                        {{ $shop['shop_name'] }}
                                        @if($shop['is_registered']) <span class="text-success">(Đã đăng ký)</span> @endif
                                    </label>
                                </div>
                                @endforeach
                            </div>
                            @else
                            Không có shop đã đăng ký
                            @endif
                        </td>

                        <td>
                            @php
                            $products = json_decode($program->products, true);
                            $productCount = is_array($products) ? count($products) : 0;
                            $price_per_product = 2000;

                            // Tính số lượng shop đã đăng ký
                            $registeredShops = collect($registeredProgramShops[$program->id] ?? [])
                            ->filter(fn($shop) => $shop['is_registered'])
                            ->count();

                            // Tổng tiền = số shop đã đăng ký * số sản phẩm * đơn giá
                            $totalPayment = $registeredShops * $productCount * $price_per_product;
                            @endphp
                            {{ $productCount }}
                        </td>

                        <td>{{ number_format($price_per_product) }}VNĐ</td>

                        <td>{{ number_format($totalPayment) }}VNĐ</td>

                        <td>
                            <a type="button" data-bs-toggle="modal" data-bs-target="#exampleModal{{ $program->id }}">
                                <li class="list-inline-item" data-bs-toggle="tooltip" data-bs-trigger="hover" data-bs-placement="top" title="Xem chi tiết">
                                    <i class="ri-eye-fill fs-16 text-primary"></i>
                                </li>
                            </a>
                            <!-- Modal chi tiết -->
                            <div class="modal fade" id="exampleModal{{$program->id}}" tabindex="-1" aria-labelledby="exampleModalLabel{{$program->id}}" aria-hidden="true">
                                <div class="modal-dialog modal-lg">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel{{$program->id}}">Danh sách sản phẩm</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                                        </div>
                                        <div class="modal-body">
                                            @if($productCount > 0)
                                            <table class="table table-bordered">
                                                <thead>
                                                    <tr>
                                                        <th>SKU</th>
                                                        <th>Tên sản phẩm</th>
                                                        <th>Hình ảnh</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($products as $product)
                                                    <tr>
                                                        <td>{{ $product['sku'] ?? 'N/A' }}</td>
                                                        <td>{{ $product['name'] ?? 'Không có tên' }}</td>
                                                        <td>
                                                            @if(!empty($product['image']))
                                                            <img src="{{ $product['image'] }}" alt="Hình ảnh" style="width: 50px; height: 50px;">
                                                            @else
                                                            Không có ảnh
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <p><strong>Tổng số sản phẩm:</strong> {{ $productCount }}</p>
                                            @else
                                            <p>Không có sản phẩm</p>
                                            @endif
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted">Đã đăng ký</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    function updateTotalPayment(programId) {
        const wrapper = document.querySelector(`.shop-checkboxes[data-program-id='${programId}']`);
        if (!wrapper) return;

        const checkboxes = wrapper.querySelectorAll("input[type='checkbox']:not(:disabled)");
        const price = parseInt(wrapper.dataset.price);
        const productCount = parseInt(wrapper.dataset.productCount);

        let selectedCount = 0;
        checkboxes.forEach(cb => {
            if (cb.checked) selectedCount++;
        });

        const total = selectedCount * productCount * price;

        const formatted = new Intl.NumberFormat('vi-VN').format(total) + ' VNĐ';
        document.getElementById(`total-payment-${programId}`).innerText = formatted;
    }

    // Khi load trang thì tính luôn tổng tiền mặc định nếu có checkbox nào đã được check
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.shop-checkboxes').forEach(wrapper => {
            const programId = wrapper.dataset.programId;
            updateTotalPayment(programId);
        });
    });
</script>

@endsection