-- Tạo role Product Manager trên production database
INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) 
VALUES (7, 'Product Manager', 'product_manager', 'Quản lý sản phẩm có quyền như seller + tạo & đăng sản phẩm như admin', NOW(), NOW())
ON DUPLICATE KEY UPDATE 
    `name` = VALUES(`name`),
    `slug` = VALUES(`slug`),
    `description` = VALUES(`description`),
    `updated_at` = NOW();
