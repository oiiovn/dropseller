import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Xử lý AJAX response khi session hết hạn
$(document).ajaxError(function(event, jqXHR, settings) {
    if (jqXHR.status === 401) {
        // Session hết hạn, chuyển hướng đến trang đăng nhập
        window.location.href = '/login';
    }
});

// Thiết lập kiểm tra phiên định kỳ (nếu cần)
function checkSession() {
    $.ajax({
        url: '/check-session',
        method: 'GET',
        success: function(response) {
            // Phiên vẫn hoạt động
        },
        error: function(xhr) {
            if (xhr.status === 401) {
                // Phiên đã hết hạn, chuyển hướng đến trang đăng nhập
                window.location.href = '/login';
            }
        }
    });
}

// Gọi kiểm tra phiên mỗi 5 phút (nếu cần)
// setInterval(checkSession, 300000);
