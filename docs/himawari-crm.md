# Himawari Bicycle CRM (Internal)

## Frontend template
- Dashboard UI được tích hợp dựa trên thư viện local: `~/Downloads/free-react-tailwind-admin-dashboard-main`.
- Điểm vào frontend: `resources/js/crm/main.jsx`.
- Route truy cập: `/crm`.

## API nội bộ
- Prefix: `/crm-api`.
- Các module: affiliates, customers, products, orders, payments, debts, deliveries, commissions, dashboard.
- Phase 2:
  - Rule hoa hồng linh hoạt: `/crm-api/commission-rules`
  - Báo cáo nâng cao tuần/tháng: `/crm-api/reports/advanced`
  - Top CTV theo kỳ: `/crm-api/reports/affiliates`
  - Cảnh báo công nợ quá hạn: `/crm-api/alerts/overdue-debts`

## RBAC
- Vai trò: `admin`, `staff`, `collaborator`.
- Bảng quyền: `crm_roles`, `crm_permissions`, `crm_role_user`, `crm_permission_role`.
- Seed dữ liệu quyền: `php artisan db:seed --class=CrmRolePermissionSeeder`.

## Phase 2 vận hành
- Command đồng bộ công nợ quá hạn: `php artisan crm:check-overdue-debts`.
- Scheduler đã cấu hình chạy command này mỗi giờ trong `app/Console/Kernel.php`.

## Chạy dự án
1. `php artisan migrate`
2. `php artisan db:seed`
3. `npm install`
4. `npm run dev`
