import React from "react";
import { api } from "../../lib/api";
import { useAuth } from "../../context/AuthContext";
import PaymentsListView from "./PaymentsListView";

export default function PaymentsListPage() {
    const { isAccounting, isAdmin } = useAuth();
    const canApprove = isAccounting || isAdmin;
    const defaultApprovalStatus = "";

    return (
        <PaymentsListView
            apiClient={api}
            canApprove={canApprove}
            defaultApprovalStatus={defaultApprovalStatus}
            resetApprovalStatus={defaultApprovalStatus}
        />
    );
}
