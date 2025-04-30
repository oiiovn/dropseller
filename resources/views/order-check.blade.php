<form method="POST" action="{{ route('order.check.send') }}">
    @csrf
    <input type="text" name="message" placeholder="Nhập nội dung cần gửi" required>
    <button type="submit">Gửi tới Telegram</button>
</form>
