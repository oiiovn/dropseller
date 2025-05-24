@extends('layout')
@section('title', 'Quyết toán tháng')

@section('main')
<style>
    .modal-dialog-right {
        position: fixed;
        right: 0;
        margin: 0;
        height: 100%;
        width: 500px;
        transform: translateX(100%);
    }

    .card {
        background-color: #f8f9fa;
        border-radius: 8px;
        box-shadow: 4px 4px 8px rgba(0, 0, 0, 0.1);
    }

FQuyes    #chatContainer {
        width: 100%;
        max-width: 100%;
        margin-top: 20px;
        padding: 20px;
        border: 2px solid #ccc;
        border-radius: 10px;
        font-family: Arial, sans-serif;
        background-color: #fff;
    }

    #chatBox {
        width: 100%;
        height: 300px; /* Hoặc auto nhưng max-height */
        max-height: 300px; /* Không vượt quá */
        border: 1px solid #ccc;
        overflow-y: auto;
        padding: 10px;
        margin-bottom: 10px;
        border-radius: 5px;
        background: #f9f9f9;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    #userInput {
        flex: 1;
        padding: 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    #sendBtn {
        padding: 10px 20px;
        border: none;
        background-color: #007bff;
        color: white;
        border-radius: 5px;
        cursor: pointer;
    }

    #sendBtn:hover {
        background-color: #0056b3;
    }

    .message {
        max-width: 100%;
        padding: 8px 12px;
        border-radius: 10px;
        word-wrap: break-word;
    }

    .user {
        align-self: flex-end;
        background-color: #dcf8c6;
        text-align: right;
    }

    .bot {
        align-self: flex-start;
        background-color: #ececec;
        text-align: left;
    }
</style>

<div class="container bg-white">
    <h4 class="pt-2 text-body fw-light">Quyết toán</h4>
    <div class="bg-white mt-2 p-4 rounded-3">

        {{-- THỐNG KÊ THÁNG TRƯỚC --}}
        @if(isset($quyet_toan_thang_truoc))
        <div class="row align-items-center border-2">
            <div class="col-md-3 align-items-center">
                <div class="card p-3 bg-white" style="height: 102px;">
                    <div>
                        <h6 class="text-body-emphasis mb-1 fw-bold">
                            <img class="pe-1" src="assets/images/svg/crypto-icons/amp.svg" alt="File Icon" style="width:10%;">
                            Chênh lệch
                            <i class="bi bi-exclamation-circle-fill text-body-secondary" role="button" data-bs-toggle="tooltip"
                                title="Nếu số tiền là số âm (–), bạn sẽ bị trừ tiền. Nếu là số dương (+), bạn sẽ được cộng thêm tiền"></i>
                        </h6>
                        <div class="small text-success mb-1" style="font-size: 10px;">
                            Tháng trước
                            <i class="bi bi-question-circle-fill text-body-tertiary ms-1" role="button" data-bs-toggle="tooltip"
                                title="{{ \Carbon\Carbon::parse($quyet_toan_thang_truoc->month . '-01')->format('01/m/Y') }} ~ {{ \Carbon\Carbon::parse($quyet_toan_thang_truoc->month . '-01')->endOfMonth()->format('d/m/Y') }}">
                            </i>
                        </div>
                    </div>
                    <h4 class="fw-bold text-body">
                        {{ number_format($quyet_toan_thang_truoc->tien_phai_thanh_toan) }}đ
                    </h4>
                </div>
            </div>

            <div class="col-auto fw-bold fs-4 mb-3">=</div>

            <div class="col-md-3">
                <div class="card p-3 bg-white" style="height: 102px;">
                    <h6 class="text-body-emphasis mb-1 fw-bold">
                        <img class="pe-1" src="https://salework.net/assets/images/apps/stock.png" alt="File Icon" style="width:10%;">
                        Thực tế(salework)
                        <i class="bi bi-exclamation-circle-fill text-body-secondary ms-1" role="button" data-bs-toggle="tooltip"
                            title="Tiền vốn sản phẩm giao thành công + Tiền quà khẩu trang từ đơn hoàn"></i>
                    </h6>
                    <div class="small text-white mb-1" style="font-size: 10px;">i</div>
                    <h4 class="fw-bold text-body">
                        {{ number_format($quyet_toan_thang_truoc->tien_thuc_te + $quyet_toan_thang_truoc->khau_trang) }}đ
                    </h4>
                </div>
            </div>

            <div class="col-auto fw-bold fs-4 mb-3">-</div>

            <div class="col-md-3">
                <div class="card p-3 bg-white" style="height: 102px;">
                    <h6 class="text-body-emphasis mb-1 fw-bold">
                        <img class="pe-1" src="https://img.icons8.com/windows/32/blog-logo.png" alt="File Icon" style="width:10%;">Thực tế(web)
                        <i class="bi bi-exclamation-circle-fill text-body-secondary ms-1" role="button" data-bs-toggle="tooltip"
                            title="Tiền đã thanh toán đơn sỉ – (Tiền huỷ + Phí dropship huỷ) – Giá vốn sản phẩm hoàn">
                        </i>
                    </h6>
                    <div class="small text-white mb-1" style="font-size: 10px;">i</div>
                    <h4 class="fw-bold text-body">
                        {{ number_format($quyet_toan_thang_truoc->total_chi) }}đ
                    </h4>
                </div>
            </div>
        </div>
        @endif

        {{-- BỘ LỌC --}}
        <!-- <div class="row mt-4">
            <div class="col-md-3">
                <input type="month" class="form-control" placeholder="Chọn tháng">
            </div>
            <div class="col-md-3">
                <input type="text" class="form-control" placeholder="Tìm kiếm ID quyết toán/điều chỉnh">
            </div>
            <div class="col-md-3">
                <button class="btn btn-outline-success" type="button">
                    Lọc
                </button>
            </div>
        </div> -->

        {{-- BẢNG DỮ LIỆU --}}
        <div class="table-responsive mt-4">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th scope="col">Mã quyết toán</th>
                        <th>Ngày tạo</th>
                        <th>Tháng quyết toán</th>
                        <th>Đã thu</th>
                        <th>Thực tế(web)</th>
                        <th>Thực tế(salework)</th>
                        <th>Chênh lệch</th>
                        <th>Chi tiết</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($quyet_toan as $item)
                    <tr>
                        <td>{{ $item->id_QT }}</td>
                        <td>{{ $item->created_at }}</td>
                        <td>{{ $item->month }}</td>
                        <td>{{ number_format($item->total_paid) }}đ</td>
                        <td>{{ number_format($item->total_chi) }}đ</td>
                        <td> {{ number_format($item->tien_thuc_te + $item->khau_trang) }}đ</td>
                        <td>{{ number_format($item->tien_phai_thanh_toan) }}đ</td>
                        <td>
                            <a href="#" class="text-primary" data-bs-toggle="modal" data-bs-target="#detailModal{{ $item->id }}">
                                Xem chi tiết
                            </a>

                            {{-- MODAL --}}
                            <div class="modal fade" id="detailModal{{ $item->id }}" tabindex="-1" aria-labelledby="modalLabel{{ $item->id }}" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-right col-12 col-md-12 m-0" style="width: 40%; max-width: none;">
                                    <div class="modal-content h-100">
                                        <div class="modal-header flex-column align-items-start">
                                            <h5 class="modal-title mt-3 mb-3" id="modalLabel{{ $item->id }}">
                                                Phân tích quyết toán tháng {{ $item->month }}
                                            </h5>
                                            <div class="d-flex p-2">
                                                <div class="me-5 text-start">
                                                    <h5 class="fw-bolder text-body-emphasis mb-1">ID quyết toán:</h5>
                                                    <h5 class="fw-normal text-body mb-0" style="font-size: 16px;">{{ $item->id_QT }}</h5>
                                                </div>
                                                <div class="text-start">
                                                    <h5 class="fw-bolder text-body-emphasis mb-1">Ngày lọc:</h5>
                                                    <h5 class="fw-normal text-body mb-0" style="font-size: 16px;">1-3-2025 ~ 31-3-2025</h5>
                                                </div>
                                            </div>

                                        </div>

                                        <div class="modal-body overflow-auto">
                                            <!-- Mục chính 1 -->
                                            <div>
                                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom p-2" style="background:rgba(0, 153, 149, 0.08);" data-bs-toggle="collapse" data-bs-target="#revenueDetail1" role="button">
                                                    <div class="fw-bold" style="font-size:22px;">
                                                        <h6 class="text-body-emphasis mb-1 fw-bold" style="font-size:22px;">
                                                            <img class="pe-1" src="https://img.icons8.com/windows/32/blog-logo.png" alt="File Icon" style="width: auto;height:30px;">Thực tế(web)
                                                            <i class="bi bi-exclamation-circle-fill text-body-secondary ms-1" role="button" data-bs-toggle="tooltip"
                                                                title="Tiền đã thanh toán đơn sỉ – (Tiền huỷ + Phí dropship huỷ) – Giá vốn sản phẩm hoàn">
                                                            </i>
                                                        </h6>
                                                    </div>
                                                    <div class="d-flex align-items-center" style="font-size:18px;">
                                                        <strong class="text-dark fw-bold">{{ number_format($item->total_chi) }} đ</strong>
                                                        <i class="bi bi-chevron-down ms-1"></i> {{-- ms-1 = margin-left: 0.25rem ~ 4px --}}
                                                    </div>
                                                </div>
                                                <div class="collapse show  pt-2 ps-1" id="revenueDetail1">
                                                    <div class="d-flex justify-content-between text-dark" style="font-size:18px;">
                                                        <span>Tổng thanh toán tiền đơn sỉ
                                                            <i class="bi bi-exclamation-circle-fill text-body-secondary ms-1" role="button" data-bs-toggle="tooltip"
                                                                title="Là số tiền bạn đã thanh toán cho tất cả đơn hàng sỉ"></i>
                                                        </span>
                                                        <span>{{ number_format($item->total_paid) }} đ</span>
                                                    </div>
                                                </div>
                                                <div class="collapse show ps-3 pt-2 ps-3" id="revenueDetail1">
                                                    <div class="d-flex justify-content-between " style="font-size:15px;">
                                                        <span>Tổng thanh toán tiền đơn huỷ
                                                            <i class="bi bi-exclamation-circle-fill text-body-secondary ms-1" role="button" data-bs-toggle="tooltip"
                                                                title="Đơn huỷ là những sản phẩm đã tạo đơn hàng trên web, nhưng sau đó bị khách huỷ, hoặc shop huỷ, hoặc hệ thống tự huỷ vì lý do nào đó"></i>
                                                        </span>
                                                        <span>{{ number_format($item->total_canceled) }} đ</span>
                                                    </div>
                                                </div>

                                                <div class="collapse show ps-3 pt-2 ps-3" id="revenueDetail1">
                                                    <div class="d-flex justify-content-between" style="font-size:15px;">
                                                        <span>Tổng thanh toán tiền đơn hoàn</span>
                                                        <span>{{ number_format($item->total_return) }} đ</span>
                                                    </div>
                                                    @if (!empty($item->shop_details))
                                                    @foreach($item->shop_details as $shop)
                                                    <div class="d-flex justify-content-between text-muted ps-4" style="font-size:14px;">
                                                        <span> •
                                                            @foreach($shops as $shop1)
                                                            @if($shop['shop_id'] == $shop1->shop_id)
                                                            {{ $shop1->shop_name }}
                                                            @endif
                                                            @endforeach :
                                                            </strong> — Hoàn: <span class="fw-bold text-danger">{{ number_format($shop['tong_tien_hoan']) }} đ</span>
                                                        </span>

                                                    </div>
                                                    @endforeach
                                                    @endif
                                                </div>
                                                <div class="collapse show ps-3 pt-2 ps-3" id="revenueDetail1">
                                                    <div class="d-flex justify-content-between " style="font-size:15px;">
                                                        <span>Tổng tiền dropship đã thu
                                                            <i class="bi bi-exclamation-circle-fill text-body-secondary ms-1" role="button" data-bs-toggle="tooltip"
                                                                title="Đếm tổng sản phẩm (trừ khẩu trang và đơn huỷ web) × 5.000đ"></i>
                                                        </span>
                                                        <span>{{ number_format($item->Drop_ships) }} đ</span>
                                                    </div>
                                                </div>

                                            </div>
                                            <hr>

                                            <!-- Mục chính 2 -->
                                            <div>
                                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom p-2" style="background:rgba(0, 153, 149, 0.08);" data-bs-toggle="collapse" data-bs-target="#revenueDetail2" role="button">
                                                    <div class="fw-bold" style="font-size:22px;">
                                                        <h6 class="text-body-emphasis mb-1 fw-bold" style="font-size:22px;">
                                                            <img class="pe-1" src="https://salework.net/assets/images/apps/stock.png" alt="File Icon" style="width:4%; ">
                                                            Thực tế(salework)
                                                            <i class="bi bi-exclamation-circle-fill text-body-secondary ms-1" role="button" data-bs-toggle="tooltip"
                                                                title="Tiền vốn sản phẩm giao thành công + Tiền quà khẩu trang từ đơn hoàn"></i>
                                                        </h6>
                                                    </div>
                                                    <div class="d-flex align-items-center" style="font-size:18px;">
                                                        <strong class="text-dark fw-bold">{{ number_format($item->tien_thuc_te + $item->khau_trang) }}đ</strong>
                                                        <i class="bi bi-chevron-down ms-1"></i> {{-- ms-1 = margin-left: 0.25rem ~ 4px --}}
                                                    </div>
                                                </div>
                                                <div class="collapse show  pt-2 ps-1" id="revenueDetail2">
                                                    <div class="d-flex justify-content-between text-dark" style="font-size:18px;">
                                                        <span>Đơn thành công
                                                            <i class="bi bi-exclamation-circle-fill text-body-secondary ms-1" role="button" data-bs-toggle="tooltip"
                                                                title="Tổng giá vốn của tất cả sản phẩm thuộc đơn giao thành công">
                                                            </i>
                                                        </span>
                                                        <span>{{ number_format($item->tien_thuc_te) }} đ</span>
                                                    </div>
                                                </div>
                                                <div class="collapse show  pt-2 ps-1" id="revenueDetail2">
                                                    <div class="d-flex justify-content-between text-dark" style="font-size:18px;">
                                                        <span>Quà tặng hoàn
                                                            <i class="bi bi-exclamation-circle-fill text-body-secondary ms-1" role="button" data-bs-toggle="tooltip"
                                                                title="Tổng giá vốn của quà tặng khẩu trang thuộc đơn bị hoàn">
                                                            </i>
                                                        </span>
                                                        <span>{{ number_format($item->khau_trang) }} đ</span>
                                                    </div>
                                                </div>


                                            </div>
                                            <hr>
                                            <!-- Mục chính 3 -->
                                            <div>
                                                <div class="d-flex justify-content-between align-items-center py-2 border-bottom p-2" style="background:rgba(0, 153, 149, 0.08);" data-bs-toggle="collapse">
                                                    <div class="fw-bold" style="font-size:22px;">
                                                        <h6 class="text-body-emphasis mb-1 fw-bold" style="font-size:22px;">
                                                            <img class="pe-1" src="assets/images/svg/crypto-icons/amp.svg" alt="File Icon" style="width: auto; height:30px;">
                                                            Chênh lệch
                                                            <i class="bi bi-exclamation-circle-fill text-body-secondary ms-1" role="button" data-bs-toggle="tooltip"
                                                                title="Nếu số tiền là số âm (–), bạn sẽ bị trừ tiền. Nếu là số dương (+), bạn sẽ được cộng thêm tiền."></i>
                                                        </h6>
                                                    </div>
                                                    <div class="d-flex align-items-center" style="font-size:22px;">
                                                        <strong class="text-dark fw-bold">{{ number_format($item->tien_phai_thanh_toan) }} đ</strong>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- AI hỏi về quyết toán -->
                                            <div  id="chatContainer">
                                                <div id="chatBox"></div>
                                                <div style="display: flex; gap: 2px;">
                                                    <input type="text" id="userInput" placeholder="Hỏi AI về thông tin quyết toán...">
                                                    <button id="sendBtn">Gửi</button>
                                                </div>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
        </div>
        </td>
        </tr>
        @endforeach
        </tbody>
        </table>
    </div>
</div>
<!-- Giả lập ai trả lời -->
<script>
    const chatBox = document.getElementById('chatBox');
    const userInput = document.getElementById('userInput');
    const sendBtn = document.getElementById('sendBtn');

    sendBtn.addEventListener('click', function() {
        sendMessage();
    });

    userInput.addEventListener('keypress', function(event) {
        if (event.key === 'Enter') {
            sendMessage();
        }
    });

    function sendMessage() {
        const text = userInput.value.trim();
        if (text !== '') {
            appendMessage('Bạn', text, 'user');
            userInput.value = '';
            setTimeout(() => {
                const botReply = getBotReply(text);
                appendMessage('DROP AI', botReply, 'bot');
            }, 500);
        }
    }

    function appendMessage(sender, text, className) {
        const message = document.createElement('div');
        message.classList.add('message', className);
        message.innerHTML = `<strong>${sender}:</strong> ${text}`;
        chatBox.appendChild(message);
        chatBox.scrollTop = chatBox.scrollHeight;
    }

    function getBotReply(userText) {
        // Giả lập trả lời AI
        return "Xin lỗi! chúng tôi đang phát triển, vui lòng thử lại sau!";
    }
</script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle=\"tooltip\"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endsection