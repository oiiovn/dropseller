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
    <h2 class="mb-4">Danh sách gói sản phẩm</h2>
    <!-- Tabs -->
    <ul class="nav nav-tabs mb-3" id="programTabs" role="tablist">
        <li class="nav-item">
            <button class="nav-link active" id="tab-all" data-bs-toggle="tab" data-bs-target="#tabAllPrograms" type="button" role="tab">
                Tất cả 
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="tab-registered" data-bs-toggle="tab" data-bs-target="#tabRegisteredPrograms" type="button" role="tab">
                Đang triển khai
            </button>
        </li>
        <li class="nav-item">
            <button class="nav-link" id="tab-tabfinish" data-bs-toggle="tab" data-bs-target="#tabfinish" type="button" role="tab">
                Đã hoàn thành
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
                        <th>Tên gói</th>
                        <th>Giờ lưu gói</th>
                        <th>Shop </th>
                        <th>Số lượng sản phẩm</th>
                        <th>Trạng thái</th>
                        <th>Xem chi tiết</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($Programs_list as $programs)
                    @php
                    $products = json_decode($programs->program->products, true);
                    $productCount = is_array($products) ? count($products) : 0;
                    @endphp
                    <tr>
                        <td>{{ $programs->id }}</td>
                        <td>{{ $programs->program->name_program ?? 'Không có tên' }}</td>
                        <td>{{ $programs->program->description ?? 'Chưa có giờ lưu' }}</td>
                        <td>{{ $programs->shop->shop_name ?? 'Không có shop' }}</td>
                        <td>{{ $productCount }}</td>
                        <td>
                            @if ($programs->status_program == 'Chưa triển khai')
                            <span class="badge bg-danger">{{ $programs->status_program }}</span>
                            @elseif ($programs->status_program == 'Đang triển khai')
                            <span class="badge bg-warning text-dark">{{ $programs->status_program }}</span>
                            @elseif ($programs->status_program == 'Đã hoàn thành')
                            <span class="badge bg-success">{{ $programs->status_program }}</span>
                            @else
                            <span class="badge bg-secondary">Không xác định</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <a type="button" data-bs-toggle="modal" data-bs-target="#exampleModal{{$programs->id}}">
                                <li class="list-inline-item" title="Xem chi tiết">
                                    <i class="ri-eye-fill fs-16 text-primary"></i>
                                </li>
                            </a>
                            <!-- Modal chi tiết -->
                            <div class="modal fade" id="exampleModal{{$programs->id}}" tabindex="-1" aria-labelledby="exampleModalLabel{{$programs->id}}" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                <div class="modal-dialog modal-xl" style="height: auto; max-height: none;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel{{$programs->id}}">Danh sách sản phẩm</h5>
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
                                                        <td>
                                                            <span class="sku-text" id="sku-{{ $loop->index }}">{{ $product['sku'] ?? 'N/A' }}</span>
                                                            <button class="btn btn-sm btn-light copy-btn" data-copy-target="sku-{{ $loop->index }}" title="Sao chép">
                                                                📋
                                                            </button>
                                                        </td>

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
                            @if ($programs->status_program == 'Đã hoàn thành'|| $programs->status_program == 'Đang triển khai')
                            <span class="badge bg-success">{{ $programs->status_program }}</span>
                            @else
                            <form action="{{ route('program.changeStatus', $programs->id) }}" method="POST" class="change-status-form">
                                @csrf
                                <input type="hidden" name="status_program" value="Đang triển khai">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Thực hiện</button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- Chương trình đã đăng ký -->
        <div class="tab-pane fade" id="tabRegisteredPrograms" role="tabpanel">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Mã </th>
                        <th>Tên gói</th>
                        <th>Giờ lưu gói</th>
                        <th>Shop </th>
                        <th>Số lượng sản phẩm</th>
                        <th>Trạng thái</th>
                        <th>Xem chi tiết</th>
                        <th>Người thực hiện</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $filteredPrograms = $Programs_list->filter(function($program) {
                    return $program->status_program === 'Đang triển khai';
                    });
                    @endphp
                    @if($filteredPrograms->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center">
                            <h5 class="text-muted">Không có chương trình nào</h5>
                        </td>
                    </tr>
                    @else
                    @foreach($filteredPrograms as $programs)
                    @php
                    $products = json_decode($programs->program->products, true);
                    $productCount = is_array($products) ? count($products) : 0;
                    @endphp
                    <tr>
                        <td>{{ $programs->id }}</td>
                        <td>{{ $programs->program->name_program ?? 'Không có tên' }}</td>
                        <td>{{ $programs->program->description ?? 'Chưa có giờ lưu' }}</td>
                        <td>{{ $programs->shop->shop_name ?? 'Không có shop' }}</td>
                        <td>{{ $productCount }}</td>
                        <td>
                            @if ($programs->status_program == 'Chưa triển khai')
                            <span class="badge bg-danger">{{ $programs->status_program }}</span>
                            @elseif ($programs->status_program == 'Đang triển khai')
                            <span class="badge bg-warning text-dark">{{ $programs->status_program }}</span>
                            @elseif ($programs->status_program == 'Đã hoàn thành')
                            <span class="badge bg-success">{{ $programs->status_program }}</span>
                            @else
                            <span class="badge bg-secondary">Không xác định</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <a type="button" data-bs-toggle="modal" data-bs-target="#exampleModal2{{$programs->id}}">
                                <li class="list-inline-item" title="Xem chi tiết">
                                    <i class="ri-eye-fill fs-16 text-primary"></i>
                                </li>
                            </a>
                            <div class="modal fade" id="exampleModal2{{$programs->id}}" tabindex="-1" aria-labelledby="exampleModalLabel2{{$programs->id}}" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                <div class="modal-dialog modal-xl" style="height: auto; max-height: none;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel2{{$programs->id}}">Danh sách sản phẩm</h5>
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
                                                        <td>
                                                            <span class="sku-text" id="sku-{{ $loop->index }}">{{ $product['sku'] ?? 'N/A' }}</span>
                                                            <button class="btn btn-sm btn-light copy-btn" data-copy-target="sku-{{ $loop->index }}" title="Sao chép">
                                                                📋
                                                            </button>
                                                        </td>

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
                        <td>{{ $programs->confirmerUser->name}}</td>
                        <td>
                            <form action="{{ route('program.changeStatus', $programs->id) }}" method="POST" class="change-status-form">
                                @csrf
                                <input type="hidden" name="status_program" value="Đã hoàn thành">
                                <button type="submit" class="btn btn-primary btn-sm w-100">Hoàn thành</button>
                            </form>
                        </td>
                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>

        <div class="tab-pane fade" id="tabfinish" role="tabpanel">
            <table class="table table-bordered">
                <thead class="thead-dark">
                    <tr>
                        <th>Mã </th>
                        <th>Tên gói</th>
                        <th>Giờ lưu gói</th>
                        <th>Shop </th>
                        <th>Số lượng sản phẩm</th>
                        <th>Trạng thái</th>
                        <th>Xem chi tiết</th>
                        <th>Người thực hiện</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                    $filteredPrograms = $Programs_list->filter(function($program) {
                    return $program->status_program === 'Đã hoàn thành';
                    });
                    @endphp
                    @if($filteredPrograms->isEmpty())
                    <tr>
                        <td colspan="7" class="text-center">
                            <h5 class="text-muted">Không có chương trình nào</h5>
                        </td>
                    </tr>
                    @else
                    @foreach($filteredPrograms as $programs)
                    @php
                    $products = json_decode($programs->program->products, true);
                    $productCount = is_array($products) ? count($products) : 0;
                    @endphp
                    <tr>
                        <td>{{ $programs->id }}</td>
                        <td>{{ $programs->program->name_program ?? 'Không có tên' }}</td>
                        <td>{{ $programs->program->description ?? 'Chưa có giờ lưu' }}</td>
                        <td>{{ $programs->shop->shop_name ?? 'Không có shop' }}</td>
                        <td>{{ $productCount }}</td>
                        <td>
                            @if ($programs->status_program == 'Chưa triển khai')
                            <span class="badge bg-danger">{{ $programs->status_program }}</span>
                            @elseif ($programs->status_program == 'Đang triển khai')
                            <span class="badge bg-warning text-dark">{{ $programs->status_program }}</span>
                            @elseif ($programs->status_program == 'Đã hoàn thành')
                            <span class="badge bg-success">{{ $programs->status_program }}</span>
                            @else
                            <span class="badge bg-secondary">Không xác định</span>
                            @endif
                        </td>
                        <td style="text-align: center;">
                            <a type="button" data-bs-toggle="modal" data-bs-target="#exampleModal11{{$programs->id}}">
                                <li class="list-inline-item" title="Xem chi tiết">
                                    <i class="ri-eye-fill fs-16 text-primary"></i>
                                </li>
                            </a>
                            <div class="modal fade" id="exampleModal11{{$programs->id}}" tabindex="-1" aria-labelledby="exampleModalLabel11{{$programs->id}}" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
                                <div class="modal-dialog modal-xl" style="height: auto; max-height: none;">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel11{{$programs->id}}">Danh sách sản phẩm</h5>
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
                                                        <td>
                                                            <span class="sku-text" id="sku-{{ $loop->index }}">{{ $product['sku'] ?? 'N/A' }}</span>
                                                            <button class="btn btn-sm btn-light copy-btn" data-copy-target="sku-{{ $loop->index }}" title="Sao chép">
                                                                📋
                                                            </button>
                                                        </td>

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
                        <td>{{ $programs->confirmerUser->name ?? "chưa có tên"}}</td>
                        <td>
                            <span class="text-muted color:success">Đã hoàn thành</span>
                        </td>

                    </tr>
                    @endforeach
                    @endif
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.copy-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const targetId = this.getAttribute('data-copy-target');
                const text = document.getElementById(targetId)?.innerText ?? '';
                if (text) {
                    navigator.clipboard.writeText(text).then(() => {
                        this.innerText = '✅';
                        setTimeout(() => this.innerText = '📋', 1000);
                    });
                }
            });
        });
    });
</script>
<script>
    document.addEventListener('submit', function(e) {
        const form = e.target;

        // Kiểm tra nếu form có class này
        if (form.classList.contains('change-status-form')) {
            e.preventDefault();

            const confirmed = confirm('Bạn có chắc chắn muốn đổi trạng thái chương trình này không?');
            if (confirmed) {
                form.submit();
            }
        }
    });
</script>
<script>
    // ✅ Khi người dùng click vào tab → lưu tab vào localStorage
    document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            const tabId = e.target.getAttribute('data-bs-target'); // ex: #tabRegisteredPrograms
            localStorage.setItem('activeTabProgram', tabId);
        });
    });

    // ✅ Khi load trang → kích hoạt tab đã lưu trước đó
    document.addEventListener('DOMContentLoaded', function () {
        const lastTab = localStorage.getItem('activeTabProgram');
        if (lastTab) {
            const triggerEl = document.querySelector(`button[data-bs-target="${lastTab}"]`);
            if (triggerEl) {
                new bootstrap.Tab(triggerEl).show(); // Kích hoạt lại tab
            }
        }
    });
</script>

@endsection