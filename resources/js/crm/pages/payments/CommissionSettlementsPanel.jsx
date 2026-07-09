import React, { useEffect, useState } from "react";
import { Link } from "react-router";
import { formatJpy } from "../../lib/api";
import { settlementStatusLabel } from "../../lib/commissionStatuses";
import TablePagination from "../../../shared/components/TablePagination";

const SETTLEMENT_STATUSES = ["pending", "paid"];

function formatDate(value) {
    if (!value) return "—";
    return String(value).slice(0, 10);
}

export default function CommissionSettlementsPanel({ apiClient, canSettle = false }) {
    const [rows, setRows] = useState([]);
    const [summary, setSummary] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");
    const [search, setSearch] = useState("");
    const [debouncedSearch, setDebouncedSearch] = useState("");
    const [status, setStatus] = useState("");
    const [periodMonth, setPeriodMonth] = useState("");
    const [meta, setMeta] = useState({ current_page: 1, last_page: 1, total: 0 });
    const [actionId, setActionId] = useState(null);
    const [message, setMessage] = useState("");

    const load = (page = 1) => {
        setLoading(true);
        setError("");

        const params = { page, per_page: 15 };
        if (debouncedSearch.trim()) params.search = debouncedSearch.trim();
        if (status) params.status = status;
        if (periodMonth) params.period_month = periodMonth;

        apiClient
            .get("/commission-settlements", { params })
            .then((response) => {
                setRows(response.data?.data || []);
                setSummary(response.data?.summary || null);
                setMeta({
                    current_page: response.data?.current_page || 1,
                    last_page: response.data?.last_page || 1,
                    total: response.data?.total || 0,
                });
            })
            .catch(() => setError("Không tải được lịch sử quyết toán hoa hồng."))
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        const timer = setTimeout(() => setDebouncedSearch(search), 300);
        return () => clearTimeout(timer);
    }, [search]);

    useEffect(() => {
        load(1);
    }, [debouncedSearch, status, periodMonth]);

    const handleSettle = async (settlementId) => {
        setActionId(settlementId);
        setMessage("");
        try {
            await apiClient.put(`/commission-settlements/${settlementId}/settle`);
            setMessage("Đã quyết toán hoa hồng cho CTV.");
            await load(meta.current_page);
        } catch (requestError) {
            setError(requestError.response?.data?.message || "Không quyết toán được.");
        } finally {
            setActionId(null);
        }
    };

    const hasFilters = Boolean(search.trim() || status || periodMonth);

    return (
        <div className="data-table-page payments-settlements min-h-0 flex-1">
            <p className="payments-page__subtitle">
                Lịch sử quyết toán hoa hồng tích luỹ cho CTV. Phiếu chờ quyết toán cần kế toán xác nhận đã chi trả.
            </p>

            {summary && (
                <div className="payments-settlements__summary">
                    <div className="payments-settlements__summary-card">
                        <p className="payments-settlements__summary-label">Phiếu chờ quyết toán</p>
                        <p className="payments-settlements__summary-value">{summary.pending_count} phiếu</p>
                    </div>
                    <div className="payments-settlements__summary-card payments-settlements__summary-card--accent">
                        <p className="payments-settlements__summary-label">Tổng chưa chi</p>
                        <p className="payments-settlements__summary-value">{formatJpy(summary.pending_amount_jpy)}</p>
                    </div>
                    <div className="payments-settlements__summary-card">
                        <p className="payments-settlements__summary-label">Tổng đã quyết toán</p>
                        <p className="payments-settlements__summary-value">{formatJpy(summary.paid_amount_jpy)}</p>
                    </div>
                </div>
            )}

            <div className="data-table-page__filters crm-filter-bar">
                <input
                    className="crm-filter-input"
                    placeholder="Tìm tên CTV, mã CTV..."
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                />
                <select className="crm-filter-select" value={status} onChange={(event) => setStatus(event.target.value)}>
                    <option value="">Trạng thái quyết toán</option>
                    {SETTLEMENT_STATUSES.map((item) => (
                        <option key={item} value={item}>
                            {settlementStatusLabel(item)}
                        </option>
                    ))}
                </select>
                <input
                    type="month"
                    className="crm-filter-select"
                    value={periodMonth}
                    onChange={(event) => setPeriodMonth(event.target.value)}
                />
                {hasFilters && (
                    <button
                        type="button"
                        className="crm-filter-clear"
                        onClick={() => {
                            setSearch("");
                            setStatus("");
                            setPeriodMonth("");
                        }}
                    >
                        Xóa bộ lọc
                    </button>
                )}
            </div>

            {message && <p className="text-sm text-emerald-700">{message}</p>}
            {error && <p className="payments-page__error">{error}</p>}

            <div className="data-table-page__body">
                <table className="crm-table min-w-full">
                    <thead>
                        <tr>
                            <th>CTV</th>
                            <th>Tháng quyết toán</th>
                            <th>Tổng hoa hồng</th>
                            <th>Trạng thái</th>
                            <th>Ngày chi trả</th>
                            <th>Người quyết toán</th>
                            <th>Ghi chú</th>
                            {canSettle && <th>Thao tác</th>}
                        </tr>
                    </thead>
                    <tbody>
                        {loading ? (
                            <tr>
                                <td colSpan={canSettle ? 8 : 7} className="py-8 text-center text-slate-500">
                                    Đang tải...
                                </td>
                            </tr>
                        ) : rows.length === 0 ? (
                            <tr>
                                <td colSpan={canSettle ? 8 : 7} className="py-8 text-center text-slate-500">
                                    Chưa có phiếu quyết toán hoa hồng
                                </td>
                            </tr>
                        ) : (
                            rows.map((settlement) => (
                                <tr key={settlement.id}>
                                    <td>
                                        <p className="font-medium">{settlement.affiliate?.full_name || "—"}</p>
                                        <p className="text-xs text-slate-500">{settlement.affiliate?.code || "—"}</p>
                                    </td>
                                    <td>{settlement.period_month}</td>
                                    <td className="font-medium text-blue-700">{formatJpy(settlement.total_amount_jpy)}</td>
                                    <td>
                                        <span
                                            className={`crm-badge ${
                                                settlement.status === "paid"
                                                    ? "bg-emerald-50 text-emerald-700"
                                                    : "bg-amber-50 text-amber-700"
                                            }`}
                                        >
                                            {settlementStatusLabel(settlement.status)}
                                        </span>
                                    </td>
                                    <td>{formatDate(settlement.paid_at)}</td>
                                    <td>{settlement.payer?.name || "—"}</td>
                                    <td className="payments-page__note-cell">
                                        {settlement.note ? (
                                            <span className="payments-page__note" title={settlement.note}>
                                                {settlement.note}
                                            </span>
                                        ) : (
                                            "—"
                                        )}
                                    </td>
                                    {canSettle && (
                                        <td>
                                            {settlement.status === "pending" ? (
                                                <button
                                                    type="button"
                                                    className="crm-btn-sm payments-page__action-approve"
                                                    disabled={actionId === settlement.id}
                                                    onClick={() => handleSettle(settlement.id)}
                                                >
                                                    Đã quyết toán
                                                </button>
                                            ) : (
                                                <Link
                                                    to="/commissions"
                                                    className="text-xs text-blue-600 hover:underline"
                                                >
                                                    Xem ví
                                                </Link>
                                            )}
                                        </td>
                                    )}
                                </tr>
                            ))
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
                    onPageChange={load}
                />
            </div>
        </div>
    );
}
