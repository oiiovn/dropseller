<form action="{{ route('program-shop.create') }}" method="POST">
    @csrf
    <label for="shop_id">Shop ID:</label>
    <input type="number" name="shop_id" required>

    <label for="program_id">Program ID:</label>
    <input type="number" name="program_id" required>

    <label for="status_program">Trạng thái chương trình:</label>
    <input type="text" name="status_program" value="chưa triển khai" required>

    <label for="status_payment">Trạng thái thanh toán:</label>
    <input type="text" name="status_payment" value="chưa thanh toán" required>

    <label for="payment_code">Mã thanh toán:</label>
    <input type="text" name="payment_code">

    <label for="confirmer">Người xác nhận:</label>
    <input type="number" name="confirmer">

    <button type="submit">Tạo Chương Trình</button>
</form>
