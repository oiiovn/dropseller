# Hướng dẫn tăng giới hạn upload file PHP

## Vấn đề hiện tại:
- `upload_max_filesize = 4M` (giới hạn file upload) ✅ ĐÃ TĂNG
- `post_max_size = 8M` (giới hạn POST data)
- `memory_limit = 128M` (giới hạn memory)

## Cách tăng giới hạn:

### 1. Tìm file php.ini:
```bash
php --ini
```

### 2. Chỉnh sửa php.ini:
```ini
upload_max_filesize = 4M  ✅ ĐÃ CẤU HÌNH
post_max_size = 8M        ✅ ĐÃ CÓ
memory_limit = 128M       ✅ ĐÃ CÓ
max_execution_time = 300
```

### 3. Restart web server:
```bash
# Nếu dùng Apache
sudo service apache2 restart

# Nếu dùng Nginx + PHP-FPM
sudo service php8.1-fpm restart
sudo service nginx restart

# Nếu dùng Laravel Valet
valet restart
```

### 4. Kiểm tra lại:
```bash
php -i | grep -E "(upload_max_filesize|post_max_size|memory_limit)"
```

## Giải pháp tạm thời:
- Chia nhỏ file Excel thành nhiều file < 4MB ✅ ĐÃ TĂNG
- Nén file Excel trước khi upload
- Sử dụng file CSV thay vì Excel (nhẹ hơn)

## Lưu ý:
- `post_max_size` phải lớn hơn `upload_max_filesize`
- `memory_limit` phải đủ để xử lý file
- `max_execution_time` phải đủ để xử lý file lớn
