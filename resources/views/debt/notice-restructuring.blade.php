@extends('debt.layout')

@section('title', 'Thông báo tái cấu trúc | Quản lý nợ')

@section('sidebar')
    <a href="{{ route('debt.dashboard') }}" class="{{ request()->routeIs('debt.dashboard') ? 'active' : '' }}"><i class="bi bi-house me-2"></i>Trang chủ</a>
    <a href="{{ route('debt.notice-restructuring') }}" class="active"><i class="bi bi-file-text me-2"></i>Thông báo tái cấu trúc</a>
    <a href="{{ route('debt.account') }}" class="{{ request()->routeIs('debt.account*') ? 'active' : '' }}"><i class="bi bi-person me-2"></i>Email & mật khẩu</a>
    <form method="POST" action="{{ route('logout') }}" class="p-2">@csrf<button type="submit" class="btn btn-outline-light btn-sm w-100">Đăng xuất</button></form>
@endsection

@section('content')
    <div class="debt-card p-4 p-md-5" style="max-width: 720px;">
        <h4 class="mb-4 text-center">THÔNG BÁO CHÍNH THỨC VỀ VIỆC TÁI CẤU TRÚC THANH TOÁN</h4>

        <p class="mb-2">Kính gửi Anh/Chị <strong>{{ $creditorName }}</strong>,</p>
        <p class="mb-3">Tôi gửi thông báo này với tinh thần minh bạch và trách nhiệm.</p>

        <p class="mb-3">Trong suốt thời gian qua, tôi luôn ưu tiên thực hiện nghĩa vụ thanh toán theo các thỏa thuận đã thống nhất. Tuy nhiên, sau khi rà soát tổng thể cơ cấu tài chính và <strong>dòng tiền</strong> hiện tại, tôi nhận thấy mô hình thanh toán cũ – đặc biệt là phần lãi suất định kỳ – đang làm suy giảm nghiêm trọng tài chính và tạo áp lực gãy <strong>dòng tiền</strong> kéo dài.</p>

        <p class="mb-3">Nếu tiếp tục duy trì cơ chế này, phần lớn nguồn tiền hàng tháng sẽ tiếp tục bị hấp thụ bởi chi phí lãi, trong khi dư nợ gốc không giảm. Mô hình đó không đảm bảo tính bền vững trung và dài hạn, và tiềm ẩn rủi ro mất cân bằng tài chính trong tương lai. Một khi <strong>dòng tiền</strong> bị phá vỡ, tiến độ thanh toán sẽ bị gián đoạn – điều này không có lợi cho bất kỳ bên nào.</p>

        <p class="mb-2"><strong>Vì vậy, tôi chủ động chuyển sang mô hình tái cấu trúc với các nguyên tắc sau:</strong></p>
        <ul class="mb-3">
            <li>Ngừng phát sinh và thanh toán lãi kể từ ngày áp dụng.</li>
            <li>Toàn bộ nguồn tiền dành cho nghĩa vụ sẽ tập trung vào hoàn trả gốc.</li>
            <li>Số tiền thanh toán hàng tháng được xác định theo năng lực tài chính thực tế.</li>
            <li>Chính sách được áp dụng thống nhất, không có ngoại lệ cá nhân.</li>
            <li>Tiến độ được cập nhật minh bạch trên hệ thống theo dõi riêng.</li>
            <li>Không phát sinh vay mới, chỗ này gán chỗ kia.</li>
            <li>Luôn trả lời tin nhắn trong khung giờ cố định 4h00-06h00 (UTC+7).</li>
        </ul>

        <p class="mb-3">Quyết định này không phải là sự né tránh. Ngược lại, đây là lựa chọn có trách nhiệm nhằm bảo toàn khả năng hoàn trả toàn bộ nghĩa vụ gốc. Tôi lựa chọn một lộ trình ổn định, dựa trên thực tế, thay vì duy trì một mô hình có thể làm gia tăng rủi ro hệ thống.</p>

        <p class="mb-3">Kế hoạch này không được xây dựng dựa trên kỳ vọng tăng thu nhập đột biến hay giả định vượt quá khả năng. Nó được thiết kế trên nguyên tắc bảo toàn <strong>dòng tiền</strong>, kiểm soát rủi ro và duy trì thanh toán liên tục cho đến khi hoàn tất.</p>

        <p class="mb-3">Tôi hiểu rằng việc thay đổi cơ chế có thể không phù hợp với kỳ vọng ban đầu. Tuy nhiên, việc yêu cầu thanh toán vượt quá năng lực thực tế chỉ làm tăng xác suất mất cân bằng tài chính, từ đó làm giảm khả năng thực hiện nghĩa vụ trong tương lai. Ngược lại, phương án tái cấu trúc tạo điều kiện để tiến trình hoàn trả diễn ra đều đặn, minh bạch và có điểm kết thúc rõ ràng.</p>

        <p class="mb-3">Tôi không tìm kiếm sự cảm thông. Tôi thực hiện một quyết định quản trị tài chính nhằm đảm bảo nghĩa vụ được hoàn thành trọn vẹn trong điều kiện thực tế hiện tại.</p>

        <p class="mb-2"><strong>Tôi cam kết:</strong></p>
        <ul class="mb-3">
            <li>Thanh toán đúng theo lộ trình công bố.</li>
            <li>Không trì hoãn khi không có lý do khách quan.</li>
            <li>Điều chỉnh tăng tốc khi <strong>dòng tiền</strong> cải thiện.</li>
            <li>Duy trì minh bạch và trách nhiệm trong toàn bộ quá trình.</li>
        </ul>

        <p class="mb-0">Tôi tin rằng lựa chọn ổn định và có kiểm soát sẽ an toàn hơn cho tất cả các bên so với một cơ chế tạo áp lực ngắn hạn nhưng tiềm ẩn rủi ro dài hạn.</p>
        <p class="mt-4 mb-0"><strong>Trân trọng.</strong></p>
    </div>
@endsection
