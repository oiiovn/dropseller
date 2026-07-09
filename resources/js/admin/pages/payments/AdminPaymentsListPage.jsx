import React from "react";
import PaymentsListView from "../../../crm/pages/payments/PaymentsListView";
import { adminApi } from "../../lib/api";

export default function AdminPaymentsListPage() {
    return (
        <PaymentsListView
            apiClient={adminApi}
            canApprove
            defaultApprovalStatus=""
            resetApprovalStatus=""
            orderLinkPath={(orderId) => `/orders/${orderId}`}
        />
    );
}
