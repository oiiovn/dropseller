import React from "react";
import { Navigate, Route, Routes } from "react-router";
import EntityListPage from "./components/EntityListPage";
import CrmLayout from "./layout/CrmLayout";
import DashboardPage from "./pages/DashboardPage";
import ReportsPage from "./pages/ReportsPage";
import CustomerFormPage from "./pages/customers/CustomerFormPage";
import CustomersListPage from "./pages/customers/CustomersListPage";
import OrderFormPage from "./pages/orders/OrderFormPage";
import OrderDetailPage from "./pages/orders/OrderDetailPage";
import OrdersListPage from "./pages/orders/OrdersListPage";
import PaymentsListPage from "./pages/payments/PaymentsListPage";
import CommissionWalletPage from "./pages/commissions/CommissionWalletPage";
import CommissionDetailPage from "./pages/commissions/CommissionDetailPage";
import { paymentStatusLabel, statusBadgeClass } from "./lib/orderStatuses";
import { formatJpy } from "./lib/api";
import CrmAccessGate from "./components/CrmAccessGate";
import { useAuth } from "./context/AuthContext";

function CrmHomePage() {
    const { isAdmin, loading } = useAuth();

    if (loading) {
        return <p className="text-sm text-slate-500">Đang tải...</p>;
    }

    if (isAdmin) {
        window.location.replace("/admin");
        return null;
    }

    return <DashboardPage />;
}

const columns = {
    commissions: [
        { key: "id", label: "#" },
        { key: "applied_rule_name", label: "Rule áp dụng" },
        { key: "approval_status", label: "Duyệt" },
        {
            key: "payment_status",
            label: "Chi trả",
            render: (row) => (
                <span className={`crm-badge ${statusBadgeClass(row.payment_status)}`}>
                    {paymentStatusLabel(row.payment_status)}
                </span>
            ),
        },
        {
            key: "commission_amount_jpy",
            label: "Hoa hồng",
            render: (row) => formatJpy(row.commission_amount_jpy),
        },
    ],
    affiliates: [
        { key: "code", label: "Mã CTV" },
        { key: "full_name", label: "Họ tên" },
        { key: "phone", label: "Điện thoại" },
        { key: "area", label: "Khu vực" },
        { key: "status", label: "Trạng thái" },
    ],
    payments: [
        { key: "id", label: "#" },
        { key: "payment_method", label: "Phương thức" },
        {
            key: "payment_status",
            label: "Trạng thái",
            render: (row) => (
                <span className={`crm-badge ${statusBadgeClass(row.payment_status)}`}>
                    {paymentStatusLabel(row.payment_status)}
                </span>
            ),
        },
        {
            key: "amount_jpy",
            label: "Số tiền",
            render: (row) => formatJpy(row.amount_jpy),
        },
    ],
    debts: [
        { key: "id", label: "#" },
        { key: "debt_status", label: "Trạng thái" },
        {
            key: "outstanding_jpy",
            label: "Còn nợ",
            render: (row) => formatJpy(row.outstanding_jpy),
        },
        { key: "due_date", label: "Hạn thanh toán" },
    ],
    commissionRules: [
        { key: "name", label: "Tên rule" },
        { key: "product_category", label: "Dòng xe" },
        { key: "rate_percent", label: "Hoa hồng (%)" },
        { key: "priority", label: "Ưu tiên" },
        {
            key: "is_active",
            label: "Kích hoạt",
            render: (row) => (row.is_active ? "Đang dùng" : "Tạm tắt"),
        },
    ],
};

export default function App() {
    return (
        <CrmLayout>
            <CrmAccessGate>
                <Routes>
                <Route index element={<CrmHomePage />} />

                <Route path="/customers" element={<CustomersListPage />} />
                <Route path="/customers/new" element={<CustomerFormPage />} />
                <Route path="/customers/:id/edit" element={<CustomerFormPage />} />

                <Route path="/orders" element={<OrdersListPage />} />
                <Route path="/orders/new" element={<OrderFormPage />} />
                <Route path="/orders/:id" element={<OrderDetailPage />} />

                <Route path="/commissions" element={<CommissionWalletPage />} />
                <Route path="/commissions/:id" element={<CommissionDetailPage />} />

                <Route path="/payments" element={<PaymentsListPage />} />
                <Route path="/debts" element={<EntityListPage title="Quản lý công nợ" endpoint="/debts" columns={columns.debts} />} />
                <Route path="/reports/revenue" element={<ReportsPage view="revenue" />} />
                <Route path="/reports/affiliates" element={<ReportsPage view="affiliates" />} />
                <Route path="/reports" element={<ReportsPage view="overview" />} />
                <Route path="*" element={<Navigate to="/" replace />} />
                </Routes>
            </CrmAccessGate>
        </CrmLayout>
    );
}
