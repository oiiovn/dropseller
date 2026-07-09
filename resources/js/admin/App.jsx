import React from "react";
import { Navigate, Route, Routes } from "react-router";
import AdminLayout from "./layout/AdminLayout";
import AdminDashboardPage from "./pages/AdminDashboardPage";
import AffiliateListPage from "./pages/AffiliateListPage";
import UserListPage from "./pages/UserListPage";
import CommissionRulesPage from "./pages/CommissionRulesPage";
import OrderListPage from "./pages/orders/OrderListPage";
import AdminOrderDetailPage from "./pages/orders/AdminOrderDetailPage";
import AdminPaymentsListPage from "./pages/payments/AdminPaymentsListPage";
import ReportsPage from "../crm/pages/ReportsPage";
import CommissionWalletPage from "../crm/pages/commissions/CommissionWalletPage";
import PinListPage from "./pages/PinListPage";
import AdminAppearancePage from "./pages/AdminAppearancePage";
import { useAuth } from "./context/AuthContext";
import { profileApi } from "./lib/api";

function AdminRoutes() {
    const { loading } = useAuth();

    if (loading) {
        return <div className="admin-shell flex min-h-screen items-center justify-center text-slate-500">Đang tải Admin...</div>;
    }

    return (
        <AdminLayout>
            <Routes>
                <Route index element={<AdminDashboardPage />} />
                <Route path="/affiliates" element={<AffiliateListPage />} />
                <Route path="/users" element={<UserListPage />} />
                <Route path="/commission-rules" element={<CommissionRulesPage />} />
                <Route path="/orders" element={<OrderListPage />} />
                <Route path="/orders/:id" element={<AdminOrderDetailPage />} />
                <Route path="/payments" element={<AdminPaymentsListPage />} />
                <Route path="/commissions" element={<CommissionWalletPage apiClient={profileApi} />} />
                <Route path="/reports/revenue" element={<ReportsPage view="revenue" apiClient={profileApi} />} />
                <Route path="/reports/affiliates" element={<ReportsPage view="affiliates" apiClient={profileApi} />} />
                <Route path="/reports" element={<ReportsPage view="overview" apiClient={profileApi} />} />
                <Route path="/pins" element={<PinListPage />} />
                <Route path="/settings/appearance" element={<AdminAppearancePage />} />
                <Route path="*" element={<Navigate to="/" replace />} />
            </Routes>
        </AdminLayout>
    );
}

export default function App() {
    return <AdminRoutes />;
}
