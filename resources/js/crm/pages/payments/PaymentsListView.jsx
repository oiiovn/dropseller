import React, { useEffect, useState } from "react";
import { Link, useSearchParams } from "react-router";
import { formatJpy } from "../../lib/api";
import {
    paymentApprovalStatusBadgeClass,
    paymentApprovalStatusLabel,
    paymentMethodLabel,
    paymentStatusLabel,
    paymentTypeLabel,
} from "../../lib/orderStatuses";
import RejectPaymentModal from "../../components/RejectPaymentModal";
import CommissionSettlementsPanel from "./CommissionSettlementsPanel";
import TablePagination from "../../../shared/components/TablePagination";

const APPROVAL_STATUSES = ["pending", "approved", "rejected"];
const SEARCH_DEBOUNCE_MS = 300;
const TABS = [
    { id: "orders", label: "Thanh toán đơn hàng" },
    { id: "commissions", label: "Quyết toán hoa hồng CTV" },
];

function formatDate(value) {
    if (!value) return "—";
    return String(value).slice(0, 10);
}

function formatPaymentNotes(payment) {
    const parts = [];
    if (payment.notes?.trim()) {
        parts.push(payment.notes.trim());
    }
    if (payment.rejection_note?.trim()) {
        parts.push(`Từ chối: ${payment.rejection_note.trim()}`);
    }
    return parts.length > 0 ? parts.join(" · ") : null;
}

export default function PaymentsListView({
    apiClient,
    canApprove = false,
    defaultApprovalStatus = "",
    orderLinkPath = (orderId) => `/orders/${orderId}`,
    resetApprovalStatus = "",
}) {
    const [searchParams, setSearchParams] = useSearchParams();
    const queryApprovalStatus = searchParams.get("approval_status") || "";
    const queryTab = searchParams.get("tab") || "orders";
    const initialApprovalStatus = queryApprovalStatus || defaultApprovalStatus;

    const [rows, setRows] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");
    const [search, setSearch] = useState("");
    const [debouncedSearch, setDebouncedSearch] = useState("");
    const [approvalStatus, setApprovalStatus] = useState(initialApprovalStatus);
    const [meta, setMeta] = useState({ current_page: 1, last_page: 1, total: 0 });
    const [actionId, setActionId] = useState(null);
    const [rejectTarget, setRejectTarget] = useState(null);
    const [rejectSaving, setRejectSaving] = useState(false);
    const [rejectError, setRejectError] = useState("");

    const load = (
        page = 1,
        activeFilters = {
            search: debouncedSearch,
            approval_status: approvalStatus,
        }
    ) => {
        setLoading(true);
        setError("");

        const params = { page, per_page: 15 };
        if (activeFilters.search.trim()) params.search = activeFilters.search.trim();
        if (activeFilters.approval_status) params.approval_status = activeFilters.approval_status;

        apiClient
            .get("/payments", { params })
            .then((response) => {
                setRows(response.data?.data || []);
                setMeta({
                    current_page: response.data?.current_page || 1,
                    last_page: response.data?.last_page || 1,
                    total: response.data?.total || 0,
                });
            })
            .catch(() => setError("Không tải được danh sách thanh toán."))
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        const timer = setTimeout(() => setDebouncedSearch(search), SEARCH_DEBOUNCE_MS);
        return () => clearTimeout(timer);
    }, [search]);

    useEffect(() => {
        setApprovalStatus(initialApprovalStatus);
    }, [initialApprovalStatus]);

    useEffect(() => {
        load(1);
    }, [debouncedSearch, approvalStatus]);

    const handleApprove = async (paymentId) => {
        setActionId(paymentId);
        try {
            await apiClient.put(`/payments/${paymentId}/approve`);
            await load(meta.current_page);
        } catch (requestError) {
            setError(requestError.response?.data?.message || "Không duyệt được thanh toán.");
        } finally {
            setActionId(null);
        }
    };

    const handleReject = async (note) => {
        if (!rejectTarget) return;
        setRejectSaving(true);
        setRejectError("");
        try {
            await apiClient.put(`/payments/${rejectTarget}/reject`, { rejection_note: note });
            setRejectTarget(null);
            await load(meta.current_page);
        } catch (requestError) {
            const message =
                requestError.response?.data?.errors?.rejection_note?.[0] ||
                requestError.response?.data?.message ||
                "Không từ chối được thanh toán.";
            setRejectError(message);
        } finally {
            setRejectSaving(false);
        }
    };

    const hasFilters = Boolean(search.trim() || approvalStatus);

    const setActiveTab = (tabId) => {
        const nextParams = new URLSearchParams(searchParams);
        if (tabId === "orders") {
            nextParams.delete("tab");
        } else {
            nextParams.set("tab", tabId);
        }
        setSearchParams(nextParams);
    };

    return (
        <section className="data-table-page payments-page crm-card">
            <header className="data-table-page__header payments-page__header">
                <h1 className="payments-page__title">Quản lý thanh toán</h1>
                <p className="payments-page__subtitle">
                    Duyệt thanh toán đơn hàng và quản lý lịch sử quyết toán hoa hồng CTV
                </p>
            </header>

            <div className="payments-page__tabs">
                {TABS.map((tab) => (
                    <button
                        key={tab.id}
                        type="button"
                        className={`payments-page__tab${queryTab === tab.id ? " payments-page__tab--active" : ""}`}
                        onClick={() => setActiveTab(tab.id)}
                    >
                        {tab.label}
                    </button>
                ))}
            </div>

            {queryTab === "commissions" ? (
                <CommissionSettlementsPanel apiClient={apiClient} canSettle={canApprove} />
            ) : (
                <>
            <div className="data-table-page__filters crm-filter-bar">
                <input
                    className="crm-filter-input"
                    placeholder="Tìm mã đơn, khách hàng..."
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                />
                <select
                    className="crm-filter-select"
                    value={approvalStatus}
                    onChange={(event) => setApprovalStatus(event.target.value)}
                >
                    <option value="">Duyệt kế toán</option>
                    {APPROVAL_STATUSES.map((status) => (
                        <option key={status} value={status}>
                            {paymentApprovalStatusLabel(status)}
                        </option>
                    ))}
                </select>
                {hasFilters && (
                    <button
                        type="button"
                        className="crm-filter-clear"
                        onClick={() => {
                            setSearch("");
                            setApprovalStatus(resetApprovalStatus);
                        }}
                    >
                        Xóa bộ lọc
                    </button>
                )}
            </div>

            {error && <p className="payments-page__error">{error}</p>}

            <div className="data-table-page__body">
                <table className="crm-table min-w-full">
                    <thead>
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Loại</th>
                            <th>Số tiền</th>
                            <th>Ngày TT</th>
                            <th>Phương thức</th>
                            <th>Duyệt KT</th>
                            <th>Trạng thái</th>
                            <th>Người gửi</th>
                            <th>Ghi chú</th>
                            {canApprove && <th>Thao tác</th>}
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr>
                                <td colSpan={canApprove ? 11 : 10} className="py-8 text-center text-slate-500">
                                    Đang tải...
                                </td>
                            </tr>
                        ) : rows.length === 0 ? (
                            <tr>
                                <td colSpan={canApprove ? 11 : 10} className="py-8 text-center text-slate-500">
                                    Không có thanh toán
                                </td>
                            </tr>
                        ) : (
                            rows.map((payment) => {
                                const noteText = formatPaymentNotes(payment);

                                return (
                                    <tr key={payment.id}>
                                        <td>
                                            {payment.order?.order_code ? (
                                                <Link
                                                    to={orderLinkPath(payment.order_id)}
                                                    className="font-medium text-blue-600 hover:underline"
                                                >
                                                    {payment.order.order_code}
                                                </Link>
                                            ) : (
                                                "—"
                                            )}
                                        </td>
                                        <td>{payment.customer?.full_name || "—"}</td>
                                        <td>{paymentTypeLabel(payment.payment_type)}</td>
                                        <td className="font-medium text-emerald-700">{formatJpy(payment.amount_jpy)}</td>
                                        <td>{formatDate(payment.payment_date)}</td>
                                        <td>{paymentMethodLabel(payment.payment_method)}</td>
                                        <td>
                                            <span className={paymentApprovalStatusBadgeClass(payment.approval_status)}>
                                                {paymentApprovalStatusLabel(payment.approval_status)}
                                            </span>
                                        </td>
                                        <td>{paymentStatusLabel(payment.payment_status)}</td>
                                        <td>{payment.recorder?.name || "—"}</td>
                                        <td className="payments-page__note-cell">
                                            {noteText ? (
                                                <span
                                                    className={`payments-page__note${payment.rejection_note ? " payments-page__note--rejected" : ""}`}
                                                    title={noteText}
                                                >
                                                    {noteText}
                                                </span>
                                            ) : (
                                                <span className="text-slate-400">—</span>
                                            )}
                                        </td>
                                        {canApprove && (
                                            <td>
                                                {payment.approval_status === "pending" ? (
                                                    <div className="flex flex-wrap gap-1">
                                                        <button
                                                            type="button"
                                                            className="crm-btn-sm payments-page__action-approve"
                                                            disabled={actionId === payment.id}
                                                            onClick={() => handleApprove(payment.id)}
                                                        >
                                                            Duyệt
                                                        </button>
                                                        <button
                                                            type="button"
                                                            className="crm-btn-sm payments-page__action-reject"
                                                            disabled={actionId === payment.id}
                                                            onClick={() => {
                                                                setRejectError("");
                                                                setRejectTarget(payment.id);
                                                            }}
                                                        >
                                                            Từ chối
                                                        </button>
                                                    </div>
                                                ) : (
                                                    <span className="text-xs text-slate-400">—</span>
                                                )}
                                            </td>
                                        )}
                                    </tr>
                                );
                            })
                        )}
                    </tbody>
                </table>
            </div>

            <div className="data-table-page__footer">
                <TablePagination
                    currentPage={meta.current_page}
                    lastPage={meta.last_page}
                    total={meta.total}
                    loading={loading}
                    onPageChange={(page) => load(page)}
                />
            </div>

            <RejectPaymentModal
                open={Boolean(rejectTarget)}
                saving={rejectSaving}
                error={rejectError}
                onClose={() => {
                    if (!rejectSaving) {
                        setRejectTarget(null);
                        setRejectError("");
                    }
                }}
                onSubmit={handleReject}
            />
                </>
            )}
        </section>
    );
}
