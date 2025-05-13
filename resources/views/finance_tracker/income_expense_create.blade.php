@extends('layout')
@section('title', 'main')

@section('main')

<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Thêm chi phí</h4>
            </div><!-- end card header -->
            <div class="card-body">
                <form action="" method="POST" enctype="multipart/form-data">

                    <div class="mb-3">
                        <label for="department">Bộ phận chịu chi phí : <b>Công Ty GUVIA</b></label>
                    </div>
                    <!-- Thêm ô trợ lý AI ngay đây -->
                    <div class="col-6 mb-3">
                        <label for="ai_assistant" data-bs-toggle="tooltip" title="Nhập câu mô tả hoặc bấm ghi âm để AI hỗ trợ">Trợ lý AI</label>
                        <div class="input-group">
                            <input type="text" name="ai_assistant" id="ai_assistant" class="form-control" placeholder="Ví dụ: Sáng nay chi 45k ship hàng về kho">
                            <button type="button" id="record_button" class="btn btn-secondary">Ra lệnh</button>
                        </div>
                    </div>


                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="payer">Người chi</label>
                            <input type="text" name="payer" id="payer" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label for="date">Ngày chi</label>
                            <input type="date" name="date" id="date" class="form-control" required>
                        </div>

                        <div class="col-md-4">
                            <label for="time">Giờ chi</label>
                            <input type="time" name="time" id="time" class="form-control">
                        </div>
                    </div>



                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="expense_category_id">Loại chi</label>
                            <select name="expense_category_id" id="expense_category_id" class="form-control" required>
                                <option value="">-- Chọn loại chi --</option>
                                <option value="">Mặt bằng</option>
                                <option value="">Lương</option>
                                <option value="">Điện nước</option>
                                <option value="">Phí vận chuyển</option>
                                <option value="">Chi phí văn phòng phẩm</option>
                                <option value="">Marketing - Quảng cáo</option>
                                <option value="">Tiếp khách - Ăn uống</option>
                                <option value="">Mua sắm trang thiết bị</option>
                                <option value="">Chi phí phần mềm - Dịch vụ</option>
                                <option value="">Thuế - Lệ phí</option>
                                <option value="">Bảo trì - Sửa chữa</option>
                            </select>
                            <div id="category_warning" class="text-danger mt-2"></div>

                        </div>

                        <div class="col-md-3">
                            <label for="amount">Số tiền</label>
                            <div class="input-group">
                                <input type="text" name="amount" class="form-control" aria-label="Số tiền">
                                <span class="input-group-text">VNĐ</span>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label for="note">Nội dung chi</label>
                            <textarea name="note" id="note" class="form-control"></textarea>
                        </div>
                    </div>


                    <div class="row mb-5">
                        <div class="col-md-5">
                            <label for="payment_method">Hình thức thanh toán</label>
                            <select name="payment_method" id="payment_method" class="form-control">
                                <option value=""></option>
                                <option>Tiền mặt</option>
                                <option>Chuyển khoản</option>
                                <option>Ví điện tử</option>
                                <option>Thẻ tín dụng</option>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="receiver">Tiền chi là của ai</label>
                            <select name="receiver" id="receiver" class="form-control">
                                <option value="cong_ty">Công ty</option>
                                <option value="nhan_vien">Nhân viên</option>
                            </select>
                        </div>
                        <div class="col-md-3 mb-3">
                            <label for="attachment" data-bs-toggle="tooltip" title="Vui lòng tải lên hóa đơn, biên lai hoặc hình ảnh chứng từ liên quan.">Hóa đơn / chứng từ</label>
                            <input type="file" name="attachment" id="attachment" class="form-control">
                        </div>

                    </div>
                    <div class="mb-3 text-end d-block  ">
                        <button type="submit" class="btn btn-info">Tạo phiếu chi</button>
                    </div>
                </form>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        // Tự động set ngày hôm nay
                        const today = new Date().toISOString().split('T')[0];
                        document.getElementById('date').value = today;

                        // Tự động set giờ hiện tại
                        const now = new Date();
                        const hours = String(now.getHours()).padStart(2, '0');
                        const minutes = String(now.getMinutes()).padStart(2, '0');
                        document.getElementById('time').value = `${hours}:${minutes}`;
                    });
                </script>
                <script>
                    document.getElementById('ai_assistant').addEventListener('change', function() {
                        const prompt = this.value;

                        if (!prompt) return;

                        fetch("{{ route('finance.ai.suggest') }}", {
                                method: "POST",
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    prompt
                                })
                            })
                            .then(response => response.json())
                            .then(data => {
                                const result = data.data;

                                // Đổ dữ liệu vào form
                                if (result.payer) document.getElementById('payer').value = result.payer;
                                if (result.amount) document.querySelector('input[name="amount"]').value = result.amount;
                                if (result.category) {
                                    const select = document.getElementById('expense_category_id');
                                    Array.from(select.options).forEach(option => {
                                        if (option.text.trim() == result.category.trim()) {
                                            option.selected = true;
                                        }
                                    });
                                }
                                if (result.note) document.getElementById('note').value = result.note;
                                if (result.payment_method) {
                                    const pay = document.getElementById('payment_method');
                                    Array.from(pay.options).forEach(option => {
                                        if (option.text.trim() == result.payment_method.trim()) {
                                            option.selected = true;
                                        }
                                    });
                                }
                            });
                    });
                </script>
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const amountInput = document.querySelector('input[name="amount"]');

                        // Khi nhập vào ô số tiền
                        amountInput.addEventListener('input', function(e) {
                            let value = e.target.value;

                            // Xóa ký tự không phải số (chỉ giữ số)
                            value = value.replace(/[^\d]/g, '');

                            // Định dạng lại có dấu phẩy
                            if (value !== '') {
                                value = parseInt(value).toLocaleString('en-US');
                            }

                            e.target.value = value;
                        });

                        // Khi submit form -> loại bỏ dấu phẩy để lưu số chính xác
                        const form = document.querySelector('form');
                        form.addEventListener('submit', function() {
                            let rawValue = amountInput.value.replace(/,/g, '');
                            amountInput.value = rawValue;
                        });
                    });
                    document.getElementById('category_warning').innerText = "Không tìm thấy danh mục '" + result.category + "' trong danh sách. Vui lòng chọn loại chi phù hợp hoặc bổ sung danh mục mới.";
                </script>
                <script>
document.addEventListener('DOMContentLoaded', function () {

    // Khi bấm nút GHI ÂM
    const recordButton = document.getElementById('record_button');
    let recognition;

    recordButton.addEventListener('click', function () {
        if (!('webkitSpeechRecognition' in window)) {
            alert("Trình duyệt không hỗ trợ ghi âm trực tiếp. Vui lòng dùng Chrome hoặc Edge.");
            return;
        }

        if (!recognition) {
            recognition = new webkitSpeechRecognition();
            recognition.lang = 'vi-VN';
            recognition.continuous = false;
            recognition.interimResults = false;

            recognition.onstart = function () {
                recordButton.innerText = "⏺ Đang ghi...";
                recordButton.classList.add('btn-danger');
            };

            recognition.onend = function () {
                recordButton.innerText = "Tôi đã nhập form";
                recordButton.classList.remove('btn-danger');
            };

            recognition.onresult = function (event) {
                const transcript = event.results[0][0].transcript;
                document.getElementById('ai_assistant').value = transcript;

                // Tự gửi sang AI
                document.getElementById('ai_assistant').dispatchEvent(new Event('change'));
            };
        }

        recognition.start();
    });

    // Khi thay đổi ô AI Assistant
    document.getElementById('ai_assistant').addEventListener('change', function () {
        const prompt = this.value;

        if (!prompt) return;

        fetch("{{ route('finance.ai.suggest') }}", {
            method: "POST",
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ prompt })
        })
        .then(response => response.json())
        .then(data => {
            const result = data.data;

            // Đổ dữ liệu vào form
            if (result.payer) document.getElementById('payer').value = result.payer;
            if (result.amount) document.querySelector('input[name="amount"]').value = result.amount;

            if (result.note) document.getElementById('note').value = result.note;

            if (result.payment_method) {
                const pay = document.getElementById('payment_method');
                Array.from(pay.options).forEach(option => {
                    if (option.text.trim() == result.payment_method.trim()) {
                        option.selected = true;
                    }
                });
            }

            // Xử lý loại chi (category)
            if (result.category) {
                const select = document.getElementById('expense_category_id');
                let found = false;

                Array.from(select.options).forEach(option => {
                    if (option.text.trim() == result.category.trim()) {
                        option.selected = true;
                        found = true;
                    }
                });

                if (!found) {
                    document.getElementById('category_warning').innerText = "🚨 Không tìm thấy danh mục '" + result.category + "'. Vui lòng chọn hoặc tạo danh mục mới.";
                } else {
                    document.getElementById('category_warning').innerText = "";
                }
            }
        });
    });
});
</script>




                <!-- end form -->
            </div><!-- end card-body -->
        </div>
    </div>
</div>

@endsection