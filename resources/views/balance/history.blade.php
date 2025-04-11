@extends('layout')

@section('title', 'Biến động số dư')

@section('main')
<div class="container-pluid mt-1 p-5 bg-white">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4>📊 Lịch sử thay đổi số dư</h4>
        <!-- <div class="badge bg-success fs-6">
            💰 Số dư hiện tại: {{ number_format(Auth::user()->total_amount) }} VND
        </div> -->
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table id="giao_dich1" class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Mã GD</th>
                        <th>Thời gian</th>
                        <th>Loại</th>
                        <th>Số tiền thay đổi</th>
                        <th>Số dư sau</th>
                        <th>Ghi chú</th>
                        <th class="d-none">ID tham chiếu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($histories as $item)
                    <tr>
                        <td>
                            <h5><code>{{ $item->transaction_code ?? '---' }}</code></h5>
                        </td>
                        <td data-order="{{ $item->created_at->timestamp }}">
                            {{ $item->created_at->format('d/m/Y H:i:s') }}
                        </td>
                        <td>
                            @switch($item->type)
                            @case('deposit') <span class="badge bg-success">Nạp tiền</span> @break
                            @case('withdraw') <span class="badge bg-danger">Rút tiền</span> @break
                            @case('order') <span class="badge bg-warning text-dark">Đơn hàng</span> @break
                            @case('refund') <span class="badge bg-info text-dark">Hoàn đơn</span> @break
                            @case('ads') <span class="badge bg-dark">Quảng cáo</span> @break
                            @case('product_fee') <span class="badge bg-secondary">Phí đăng sản phẩm</span> @break
                            @default <span class="badge bg-secondary">{{ ucfirst($item->type) }}</span>
                            @endswitch
                        </td>
                        <td class="fw-bold {{ $item->amount_change >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $item->amount_change >= 0 ? '+' : '-' }}{{ number_format(abs($item->amount_change)) }} VND
                        </td>
                        <td>{{ number_format($item->balance_after) }} VND</td>
                        <td style="width:40%">{{ $item->note ?? '-' }}</td>
                        <td class="d-none">{{ $item->reference_id }}</td>
                    </tr>

                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
<script>
    document.addEventListener('click', function(e) {
        const icon = e.target.closest('.order-link .icon');
        if (!icon) return;
        const orderLink = icon.closest('.order-link');
        const orderCode = orderLink.getAttribute('data-order-code');
        if (!orderCode) return;
        if (icon.dataset.throttled === "true") return;
        icon.dataset.throttled = "true";
    });
    $(document).ready(function() {
        $('#giao_dich1').DataTable({
            "searching": true,
            "paging": true,
            "ordering": true,
            "info": true,
            "lengthMenu": [10, 20, 50, 100, 150],
            "order": [
                [1, "desc"],
                [6, "desc"]
            ],
            "columns": [
                null, null, null, null, null, null,null
            ],
            "language": {
                "search": "",
                "zeroRecords": "Không tìm thấy dữ liệu",
                "info": "Hiển thị _START_ đến _END_ của _TOTAL_ giao dịch",
                "infoEmpty": "Không có dữ liệu để hiển thị",
                "infoFiltered": "(lọc từ tổng số _MAX_ mục)",
                "lengthMenu": "Hiển thị _MENU_ giao dịch",
                "paginate": {
                    "first": "Trang đầu",
                    "last": "Trang cuối",
                    "next": "Tiếp theo",
                    "previous": "Quay lại"
                }
            }
        });
    });
</script>
{{-- CDN cho DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

@endsection