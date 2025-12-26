-- Kiểm tra ID tiếp theo cho role Product Manager
SELECT MAX(id) as max_id FROM roles;
SELECT AUTO_INCREMENT as next_id FROM information_schema.tables WHERE table_name = 'roles' AND table_schema = DATABASE();
