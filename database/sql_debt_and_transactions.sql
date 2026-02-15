-- ============================================================
-- SQL tạo bảng liên quan module Quản lý nợ & Lịch sử Pay2s
-- (Chuẩn MySQL; bảng `users` đã tồn tại)
-- ============================================================

-- 1. Thêm cột personal_code vào users (đăng nhập chủ nợ) — chạy an toàn nếu cột đã có
SET @col_exists = (
  SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
  WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'users' AND COLUMN_NAME = 'personal_code'
);
SET @sql = IF(@col_exists = 0,
  'ALTER TABLE `users` ADD COLUMN `personal_code` VARCHAR(50) NULL UNIQUE AFTER `referral_code`',
  'SELECT 1'
);
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 2. Bảng chủ nợ (creditor = user được con nợ cấp mã, debtor = admin/con nợ)
CREATE TABLE IF NOT EXISTS `debt_creditors` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `user_id` BIGINT UNSIGNED NOT NULL,
  `debtor_user_id` BIGINT UNSIGNED NOT NULL,
  `total_debt` DECIMAL(18, 2) NOT NULL DEFAULT 0,
  `phone` VARCHAR(20) NULL,
  `notes` TEXT NULL,
  `restructuring_date` DATE NULL COMMENT 'Ngày tái cấu trúc',
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `debt_creditors_user_id_debtor_user_id_unique` (`user_id`, `debtor_user_id`),
  CONSTRAINT `debt_creditors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `debt_creditors_debtor_user_id_foreign` FOREIGN KEY (`debtor_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- 3. Thêm restructuring_date nếu bảng cũ chưa có cột này
SET @col_exists = (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'debt_creditors' AND COLUMN_NAME = 'restructuring_date');
SET @sql = IF(@col_exists = 0, 'ALTER TABLE `debt_creditors` ADD COLUMN `restructuring_date` DATE NULL COMMENT ''Ngày tái cấu trúc'' AFTER `notes`', 'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 4. Kế hoạch trả nợ (tỉ lệ % từng tháng)
CREATE TABLE IF NOT EXISTS `debt_repayment_plans` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `debt_creditor_id` BIGINT UNSIGNED NOT NULL,
  `monthly_percent` DECIMAL(5, 2) NOT NULL COMMENT '% tổng thu nhập tháng',
  `pay_day_of_month` TINYINT UNSIGNED NOT NULL COMMENT 'Ngày trong tháng nhận tiền (1-31)',
  `start_at` DATE NULL,
  `end_at` DATE NULL,
  `notes` TEXT NULL,
  `is_active` TINYINT(1) NOT NULL DEFAULT 1,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `debt_repayment_plans_debt_creditor_id_foreign` FOREIGN KEY (`debt_creditor_id`) REFERENCES `debt_creditors` (`id`) ON DELETE CASCADE
);

-- 5. Thu nhập theo tháng (con nợ nhập từng tháng)
CREATE TABLE IF NOT EXISTS `debt_monthly_incomes` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `debtor_user_id` BIGINT UNSIGNED NOT NULL,
  `month` TINYINT UNSIGNED NOT NULL,
  `year` SMALLINT UNSIGNED NOT NULL,
  `amount` DECIMAL(18, 2) NOT NULL,
  `notes` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  UNIQUE KEY `debt_monthly_incomes_debtor_user_id_month_year_unique` (`debtor_user_id`, `month`, `year`),
  CONSTRAINT `debt_monthly_incomes_debtor_user_id_foreign` FOREIGN KEY (`debtor_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
);

-- 6. Phân bổ từng kỳ (mỗi chủ nợ mỗi tháng một hoặc nhiều dòng)
CREATE TABLE IF NOT EXISTS `debt_distributions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `debt_monthly_income_id` BIGINT UNSIGNED NOT NULL,
  `debt_creditor_id` BIGINT UNSIGNED NOT NULL,
  `amount` DECIMAL(18, 2) NOT NULL,
  `percent_applied` DECIMAL(5, 2) NULL,
  `transaction_code` VARCHAR(100) NOT NULL UNIQUE COMMENT 'Mã giao dịch đối chiếu khi chuyển khoản',
  `status` ENUM('pending', 'paid') NOT NULL DEFAULT 'pending',
  `paid_at` TIMESTAMP NULL,
  `bank_transaction_ref` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `debt_distributions_debt_monthly_income_id_foreign` FOREIGN KEY (`debt_monthly_income_id`) REFERENCES `debt_monthly_incomes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `debt_distributions_debt_creditor_id_foreign` FOREIGN KEY (`debt_creditor_id`) REFERENCES `debt_creditors` (`id`) ON DELETE CASCADE
);

-- 6b. Ghi chép nợ cũ (từng khoản theo chủ nợ, admin nhập)
CREATE TABLE IF NOT EXISTS `debt_old_debt_items` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `debt_creditor_id` BIGINT UNSIGNED NOT NULL,
  `code` VARCHAR(100) NULL COMMENT 'Mã',
  `principal_amount` DECIMAL(18, 2) NOT NULL DEFAULT 0 COMMENT 'Số tiền gốc',
  `notes` TEXT NULL,
  `sort_order` SMALLINT UNSIGNED NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `debt_old_debt_items_debt_creditor_id_foreign` FOREIGN KEY (`debt_creditor_id`) REFERENCES `debt_creditors` (`id`) ON DELETE CASCADE
);

-- 7. Giao dịch ngân hàng (đồng bộ từ Pay2s)
CREATE TABLE IF NOT EXISTS `transactions` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `bank` VARCHAR(255) NULL,
  `account_number` VARCHAR(255) NULL,
  `transaction_date` DATE NULL,
  `transaction_id` VARCHAR(255) NULL,
  `amount` DECIMAL(15, 2) NULL,
  `type` VARCHAR(255) NULL,
  `description` TEXT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
);

-- 8. Log lần fetch (tùy chọn)
CREATE TABLE IF NOT EXISTS `fetch_logs` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `last_fetch_date` DATETIME NOT NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL
);
