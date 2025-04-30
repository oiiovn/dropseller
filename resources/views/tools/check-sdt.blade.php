@extends('layout')

@section('title', 'Gửi tin Telegram')

@section('main')
<div class="container mt-5">
    <h2>Gửi tin nhắn tới @le_van_manh_bot</h2>

    <form id="telegram-form">
        <div class="mb-3">
            <label for="message" class="form-label">Nội dung tin nhắn</label>
            <textarea class="form-control" id="message" rows="4" placeholder="Nhập nội dung tin nhắn" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Gửi tin nhắn</button>
    </form>

    <div id="result" class="mt-3"></div>
</div>

<script>
document.getElementById('telegram-form').addEventListener('submit', function(e) {
    e.preventDefault();

    const message = document.getElementById('message').value.trim();

    fetch('/telegram_sender.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ message: message })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Kết quả trả về:', data); // ➔ Xem kết quả trả về trong console

        const resultDiv = document.getElementById('result');

        if (data.status === 'success') {
            resultDiv.innerHTML = '<div class="alert alert-success">✅ Gửi thành công!</div>';
        } else {
            let errorMsg = data.error ? data.error : 'Không rõ lỗi!';
            resultDiv.innerHTML = `<div class="alert alert-danger">❌ Gửi thất bại!<br>Lỗi: ${errorMsg}</div>`;
        }
    })
    .catch(error => {
        console.error('Lỗi hệ thống:', error);
        document.getElementById('result').innerHTML = '<div class="alert alert-danger">❌ Có lỗi hệ thống xảy ra!</div>';
    });
});
</script>
@endsection
