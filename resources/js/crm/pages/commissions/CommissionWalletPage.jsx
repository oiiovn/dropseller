import React, { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { api, formatJpy } from "../../lib/api";
import {
    COMMISSION_WALLET_STATUS_LABELS,
    commissionWalletStatusBadgeClass,
    commissionWalletStatusLabel,
    settlementStatusLabel,
} from "../../lib/commissionStatuses";
import { useRoleAuth } from "../../../shared/hooks/useRoleAuth";
import TablePagination from "../../../shared/components/TablePagination";

const SEARCH_DEBOUNCE_MS = 300;
const AFFILIATE_STATUSES = [
    { value: "active", label: "Đang hoạt động" },
    { value: "inactive", label: "Ngưng hoạt động" },
    { value: "suspended", label: "Tạm khóa" },
];
const WALLET_STATUSES = ["pending_record", "credited", "pending_settlement", "settled"];
const SETTLEMENT_STATUSES = ["pending", "paid"];

function currentMonthValue() {
    return new Date().toISOString().slice(0, 7);
}

function SummaryCard({ label, value, accent = false }) {
    return (
        <div className={`rounded-xl border p-4 ${accent ? "border-blue-200 bg-blue-50" : "border-slate-200 bg-white"}`}>
            <p className="text-xs uppercase tracking-wide text-slate-500">{label}</p>
            <p className={`mt-1 text-xl font-bold ${accent ? "text-blue-700" : "text-slate-900"}`}>{value}</p>
        </div>
    );
}

export default function CommissionWalletPage({ apiClient = api }) {
    const { isCollaborator, isAccounting, isAdmin } = useRoleAuth();
    const canSettle = isAccounting || isAdmin;
    const canFilterList = !isCollaborator;

    const [rows, setRows] = useState([]);
    const [totals, setTotals] = useState(null);
    const [listMeta, setListMeta] = useState({ current_page: 1, last_page: 1, total: 0 });
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");
    const [selectedAffiliateId, setSelectedAffiliateId] = useState(null);
    const [detail, setDetail] = useState(null);
    const [commissionsMeta, setCommissionsMeta] = useState({ current_page: 1, last_page: 1, total: 0 });
    const [detailLoading, setDetailLoading] = useState(false);
    const [periodMonth, setPeriodMonth] = useState(currentMonthValue());
    const [actionMessage, setActionMessage] = useState("");

    const [listSearch, setListSearch] = useState("");
    const [debouncedListSearch, setDebouncedListSearch] = useState("");
    const [listPeriodMonth, setListPeriodMonth] = useState(currentMonthValue());
    const [affiliateStatus, setAffiliateStatus] = useState("");
    const [unpaidOnly, setUnpaidOnly] = useState(false);
    const [pendingSettlementOnly, setPendingSettlementOnly] = useState(false);

    const [commissionSearch, setCommissionSearch] = useState("");
    const [debouncedCommissionSearch, setDebouncedCommissionSearch] = useState("");
    const [walletStatus, setWalletStatus] = useState("");
    const [settlementStatus, setSettlementStatus] = useState("");

    useEffect(() => {
        const timer = setTimeout(() => setDebouncedListSearch(listSearch), SEARCH_DEBOUNCE_MS);
        return () => clearTimeout(timer);
    }, [listSearch]);

    useEffect(() => {
        const timer = setTimeout(() => setDebouncedCommissionSearch(commissionSearch), SEARCH_DEBOUNCE_MS);
        return () => clearTimeout(timer);
    }, [commissionSearch]);

    const buildListParams = useCallback(
        (page = 1) => {
            const params = { page, per_page: 15, period_month: listPeriodMonth };
            if (debouncedListSearch.trim()) params.search = debouncedListSearch.trim();
            if (affiliateStatus) params.status = affiliateStatus;
            if (unpaidOnly) params.unpaid_only = 1;
            if (pendingSettlementOnly) params.has_pending_settlement = 1;
            return params;
        },
        [debouncedListSearch, listPeriodMonth, affiliateStatus, unpaidOnly, pendingSettlementOnly]
    );

    const buildDetailParams = useCallback(
        (page = 1) => {
            const params = { period_month: periodMonth, page, per_page: 15 };
            if (debouncedCommissionSearch.trim()) params.search = debouncedCommissionSearch.trim();
            if (walletStatus) params.wallet_status = walletStatus;
            if (settlementStatus) params.settlement_status = settlementStatus;
            return params;
        },
        [periodMonth, debouncedCommissionSearch, walletStatus, settlementStatus]
    );

    const loadList = useCallback(
        (page = 1) => {
            setLoading(true);
            apiClient.get("/commission-wallets", { params: buildListParams(page) })
                .then((response) => {
                    setRows(response.data?.data ?? []);
                    setTotals(response.data?.totals ?? null);
                    setListMeta({
                        current_page: response.data?.current_page ?? 1,
                        last_page: response.data?.last_page ?? 1,
                        total: response.data?.total ?? 0,
                    });
                })
                .catch(() => setError("Không tải được ví hoa hồng."))
                .finally(() => setLoading(false));
        },
        [buildListParams, apiClient]
    );

    const loadDetail = useCallback(
        (affiliateId, page = 1) => {
            if (!affiliateId) return;
            setDetailLoading(true);
            apiClient.get(`/commission-wallets/${affiliateId}`, { params: buildDetailParams(page) })
                .then((response) => {
                    setDetail(response.data);
                    setCommissionsMeta({
                        current_page: response.data?.commissions_current_page ?? 1,
                        last_page: response.data?.commissions_last_page ?? 1,
                        total: response.data?.commissions_total ?? 0,
                    });
                })
                .catch(() => setActionMessage("Không tải được chi tiết ví hoa hồng."))
                .finally(() => setDetailLoading(false));
        },
        [buildDetailParams, apiClient]
    );

    useEffect(() => {
        loadList(1);
    }, [loadList]);

    useEffect(() => {
        if (selectedAffiliateId) {
            loadDetail(selectedAffiliateId, 1);
        }
    }, [selectedAffiliateId, loadDetail]);

    useEffect(() => {
        if (isCollaborator && rows.length === 1 && !selectedAffiliateId) {
            setSelectedAffiliateId(rows[0].affiliate_id);
        }
    }, [isCollaborator, rows, selectedAffiliateId]);

    const handleCreateSettlement = async () => {
        if (!selectedAffiliateId) return;
        setActionMessage("");
        try {
            await apiClient.post(`/commission-wallets/${selectedAffiliateId}/settlements`, { period_month: periodMonth });
            setActionMessage("Đã tạo phiếu quyết toán tháng.");
            loadDetail(selectedAffiliateId, commissionsMeta.current_page);
            loadList(listMeta.current_page);
        } catch (err) {
            setActionMessage(err.response?.data?.message || "Không tạo được phiếu quyết toán.");
        }
    };

    const handleSettle = async (settlementId) => {
        setActionMessage("");
        try {
            await apiClient.put(`/commission-settlements/${settlementId}/settle`);
            setActionMessage("Đã quyết toán và chuyển sang trạng thái đã chi trả.");
            loadDetail(selectedAffiliateId, commissionsMeta.current_page);
            loadList(listMeta.current_page);
        } catch (err) {
            setActionMessage(err.response?.data?.message || "Không quyết toán được.");
        }
    };

    const clearListFilters = () => {
        setListSearch("");
        setListPeriodMonth(currentMonthValue());
        setAffiliateStatus("");
        setUnpaidOnly(false);
        setPendingSettlementOnly(false);
    };

    const clearDetailFilters = () => {
        setCommissionSearch("");
        setWalletStatus("");
        setSettlementStatus("");
    };

    const hasListFilters = Boolean(
        listSearch.trim() || affiliateStatus || unpaidOnly || pendingSettlementOnly || listPeriodMonth !== currentMonthValue()
    );
    const hasDetailFilters = Boolean(commissionSearch.trim() || walletStatus || settlementStatus);

    const selectedRow = rows.find((row) => row.affiliate_id === selectedAffiliateId);
    const commissions = detail?.commissions ?? [];
    const listPeriodLabel = listPeriodMonth || currentMonthValue();

    return (
        <div className="flex min-h-0 flex-1 flex-col gap-4">
            <section className="crm-card shrink-0">
                <div className="mb-4">
                    <h2 className="text-lg font-semibold">Ví hoa hồng tích luỹ</h2>
                    <p className="mt-1 text-sm text-slate-500">
                        Đơn hoàn thành sẽ tự động phát sinh hoa hồng và cộng vào ví CTV. Hoa hồng chưa được chi trả ngay,
                        mà sẽ được tổng hợp để kế toán quyết toán vào cuối tháng.
                    </p>
                </div>

                {totals && (
                    <div className="grid gap-3 md:grid-cols-2 xl:grid-cols-5">
                        <SummaryCard label="Tổng hoa hồng chưa quyết toán" value={formatJpy(totals.unpaid_balance_jpy)} accent />
                        <SummaryCard label="Hoa hồng đã quyết toán" value={formatJpy(totals.paid_balance_jpy)} />
                        <SummaryCard
                            label={`Hoa hồng tháng ${listPeriodLabel}`}
                            value={formatJpy(totals.current_month_commission_jpy)}
                        />
                        <SummaryCard label="Số đơn phát sinh hoa hồng" value={`${totals.current_month_order_count} đơn`} />
                        {canFilterList && (
                            <SummaryCard
                                label="Phiếu QT chờ duyệt"
                                value={`${totals.pending_settlements_count ?? 0} phiếu`}
                                accent={Number(totals.pending_settlements_count) > 0}
                            />
                        )}
                    </div>
                )}
            </section>

            {error && <p className="shrink-0 text-sm text-red-600">{error}</p>}

            {loading && isCollaborator && !selectedAffiliateId && (
                <p className="text-sm text-slate-500">Đang tải ví hoa hồng...</p>
            )}

            {!error && canFilterList && !selectedAffiliateId && (
                <section className="data-table-page crm-card min-h-0 flex-1">
                    <h3 className="data-table-page__header text-base font-semibold">Danh sách CTV</h3>

                    <div className="data-table-page__filters crm-filter-bar">
                        <input
                            className="crm-filter-input"
                            placeholder="Tìm tên, mã CTV, SĐT..."
                            value={listSearch}
                            onChange={(event) => setListSearch(event.target.value)}
                        />
                        <input
                            type="month"
                            className="crm-filter-select"
                            value={listPeriodMonth}
                            title="Tháng xem hoa hồng phát sinh"
                            onChange={(event) => setListPeriodMonth(event.target.value)}
                        />
                        <select
                            className="crm-filter-select"
                            value={affiliateStatus}
                            onChange={(event) => setAffiliateStatus(event.target.value)}
                        >
                            <option value="">Trạng thái CTV</option>
                            {AFFILIATE_STATUSES.map((item) => (
                                <option key={item.value} value={item.value}>
                                    {item.label}
                                </option>
                            ))}
                        </select>
                        <label className="flex shrink-0 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                            <input
                                type="checkbox"
                                checked={unpaidOnly}
                                onChange={(event) => setUnpaidOnly(event.target.checked)}
                            />
                            Còn chưa quyết toán
                        </label>
                        <label className="flex shrink-0 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                            <input
                                type="checkbox"
                                checked={pendingSettlementOnly}
                                onChange={(event) => setPendingSettlementOnly(event.target.checked)}
                            />
                            Có phiếu QT chờ
                        </label>
                        {hasListFilters && (
                            <button type="button" className="crm-filter-clear" onClick={clearListFilters}>
                                Xóa bộ lọc
                            </button>
                        )}
                    </div>

                    <div className="data-table-page__messages">
                        {loading && <p className="text-sm text-slate-500">Đang tải...</p>}
                    </div>
                    {!loading && (
                        <>
                            <div className="data-table-page__body">
                                <table className="crm-table min-w-full">
                                    <thead>
                                        <tr>
                                            <th>CTV</th>
                                            <th>Chưa quyết toán</th>
                                            <th>Đã quyết toán</th>
                                            <th>Tháng {listPeriodLabel}</th>
                                            <th>Số đơn</th>
                                            <th>Phiếu QT chờ</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {rows.length === 0 && (
                                            <tr>
                                                <td colSpan={7} className="py-6 text-center text-sm text-slate-500">
                                                    Không có CTV phù hợp bộ lọc.
                                                </td>
                                            </tr>
                                        )}
                                        {rows.map((row) => (
                                            <tr key={row.affiliate_id}>
                                                <td>
                                                    <p className="font-medium">{row.full_name}</p>
                                                    <p className="text-xs text-slate-500">
                                                        {row.code}
                                                        {row.phone ? ` · ${row.phone}` : ""}
                                                    </p>
                                                </td>
                                                <td>{formatJpy(row.unpaid_balance_jpy)}</td>
                                                <td>{formatJpy(row.paid_balance_jpy)}</td>
                                                <td>{formatJpy(row.current_month_commission_jpy)}</td>
                                                <td>{row.current_month_order_count}</td>
                                                <td>
                                                    {row.pending_settlements_count > 0 ? (
                                                        <span className="font-medium text-amber-700">
                                                            {row.pending_settlements_count} · {formatJpy(row.pending_settlements_amount_jpy)}
                                                        </span>
                                                    ) : (
                                                        "—"
                                                    )}
                                                </td>
                                                <td>
                                                    <button
                                                        type="button"
                                                        className="text-sm text-blue-600 hover:underline"
                                                        onClick={() => {
                                                            setPeriodMonth(listPeriodMonth);
                                                            setSelectedAffiliateId(row.affiliate_id);
                                                            clearDetailFilters();
                                                        }}
                                                    >
                                                        Chi tiết
                                                    </button>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            <div className="data-table-page__footer">
                                <TablePagination
                                    currentPage={listMeta.current_page}
                                    lastPage={listMeta.last_page}
                                    total={listMeta.total}
                                    loading={loading}
                                    onPageChange={loadList}
                                />
                            </div>
                        </>
                    )}
                </section>
            )}

            {selectedAffiliateId && (
                <section className="data-table-page crm-card min-h-0 flex-1">
                    <div className="data-table-page__header flex flex-wrap items-center justify-between gap-3">
                        <div>
                            <h3 className="text-base font-semibold">
                                Chi tiết ví — {selectedRow?.full_name || detail?.summary?.wallet?.affiliate?.full_name}
                            </h3>
                            {canFilterList && (
                                <button
                                    type="button"
                                    className="mt-1 text-sm text-slate-500 hover:underline"
                                    onClick={() => {
                                        setSelectedAffiliateId(null);
                                        setDetail(null);
                                        clearDetailFilters();
                                    }}
                                >
                                    ← Quay lại danh sách
                                </button>
                            )}
                        </div>
                        <div className="flex flex-wrap items-center gap-2">
                            <label className="text-sm text-slate-600">
                                Tháng
                                <input
                                    type="month"
                                    className="ml-2 rounded-lg border border-slate-200 px-2 py-1 text-sm"
                                    value={periodMonth}
                                    onChange={(event) => setPeriodMonth(event.target.value)}
                                />
                            </label>
                            {canSettle && (
                                <button type="button" className="crm-btn-secondary" onClick={handleCreateSettlement}>
                                    Tạo phiếu quyết toán
                                </button>
                            )}
                        </div>
                    </div>

                    {(canFilterList || canSettle) && (
                        <div className="data-table-page__filters crm-filter-bar">
                            <input
                                className="crm-filter-input"
                                placeholder="Tìm mã đơn, tên khách..."
                                value={commissionSearch}
                                onChange={(event) => setCommissionSearch(event.target.value)}
                            />
                            <select
                                className="crm-filter-select"
                                value={walletStatus}
                                onChange={(event) => setWalletStatus(event.target.value)}
                            >
                                <option value="">Trạng thái hoa hồng</option>
                                {WALLET_STATUSES.map((status) => (
                                    <option key={status} value={status}>
                                        {COMMISSION_WALLET_STATUS_LABELS[status] || status}
                                    </option>
                                ))}
                            </select>
                            {canSettle && (
                                <select
                                    className="crm-filter-select"
                                    value={settlementStatus}
                                    onChange={(event) => setSettlementStatus(event.target.value)}
                                >
                                    <option value="">Phiếu quyết toán</option>
                                    {SETTLEMENT_STATUSES.map((status) => (
                                        <option key={status} value={status}>
                                            {settlementStatusLabel(status)}
                                        </option>
                                    ))}
                                </select>
                            )}
                            {hasDetailFilters && (
                                <button type="button" className="crm-filter-clear" onClick={clearDetailFilters}>
                                    Xóa bộ lọc
                                </button>
                            )}
                        </div>
                    )}

                    <div className="data-table-page__messages">
                        {actionMessage && <p className="text-sm text-blue-700">{actionMessage}</p>}
                        {detailLoading && <p className="text-sm text-slate-500">Đang tải chi tiết...</p>}
                    </div>

                    {detail?.summary && (
                        <div className="mb-4 grid shrink-0 gap-3 md:grid-cols-4">
                            <SummaryCard label="Chưa quyết toán" value={formatJpy(detail.summary.unpaid_balance_jpy)} accent />
                            <SummaryCard label="Đã quyết toán" value={formatJpy(detail.summary.paid_balance_jpy)} />
                            <SummaryCard
                                label={`Hoa hồng tháng ${periodMonth}`}
                                value={formatJpy(detail.summary.current_month_commission_jpy)}
                            />
                            <SummaryCard label="Số đơn phát sinh" value={`${detail.summary.current_month_order_count} đơn`} />
                        </div>
                    )}

                    {detail?.settlements?.length > 0 && (
                        <div className="mb-4 shrink-0">
                            <h4 className="mb-2 font-semibold">Phiếu quyết toán</h4>
                            <div className="space-y-2">
                                {detail.settlements.map((settlement) => (
                                    <div
                                        key={settlement.id}
                                        className="flex flex-wrap items-center justify-between gap-2 rounded-lg border border-slate-100 p-3 text-sm"
                                    >
                                        <div>
                                            <p className="font-medium">
                                                Tháng {settlement.period_month} — {formatJpy(settlement.total_amount_jpy)}
                                            </p>
                                            <p className="text-slate-500">{settlementStatusLabel(settlement.status)}</p>
                                        </div>
                                        {canSettle && settlement.status === "pending" && (
                                            <button
                                                type="button"
                                                className="crm-btn-primary"
                                                onClick={() => handleSettle(settlement.id)}
                                            >
                                                Đã quyết toán
                                            </button>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    )}

                    {detail && (
                        <>
                            <h4 className="mb-2 shrink-0 font-semibold">Đơn phát sinh hoa hồng</h4>
                            <div className="data-table-page__body">
                                <table className="crm-table min-w-full">
                                    <thead>
                                        <tr>
                                            <th>Mã đơn</th>
                                            <th>Khách hàng</th>
                                            <th>Ngày ghi ví</th>
                                            <th>Giá trị đơn</th>
                                            <th>Rule áp dụng</th>
                                            <th>Hoa hồng</th>
                                            <th>Trạng thái</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {commissions.length === 0 && !detailLoading && (
                                            <tr>
                                                <td colSpan={7} className="py-6 text-center text-sm text-slate-500">
                                                    Không có hoa hồng phù hợp bộ lọc.
                                                </td>
                                            </tr>
                                        )}
                                        {commissions.map((commission) => (
                                            <tr key={commission.id}>
                                                <td>
                                                    <Link
                                                        to={`/orders/${commission.order_id}`}
                                                        className="text-blue-600 hover:underline"
                                                    >
                                                        {commission.order?.order_code || `#${commission.order_id}`}
                                                    </Link>
                                                </td>
                                                <td>{commission.order?.customer?.full_name || "—"}</td>
                                                <td>{commission.credited_at ? String(commission.credited_at).slice(0, 10) : "—"}</td>
                                                <td>{formatJpy(commission.order?.total_amount_jpy)}</td>
                                                <td>{commission.applied_rule_name}</td>
                                                <td>{formatJpy(commission.commission_amount_jpy)}</td>
                                                <td>
                                                    <span
                                                        className={`crm-badge ${commissionWalletStatusBadgeClass(commission.wallet_status)}`}
                                                    >
                                                        {commissionWalletStatusLabel(commission.wallet_status)}
                                                    </span>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                            <div className="data-table-page__footer">
                                <TablePagination
                                    currentPage={commissionsMeta.current_page}
                                    lastPage={commissionsMeta.last_page}
                                    total={commissionsMeta.total}
                                    loading={detailLoading}
                                    onPageChange={(page) => loadDetail(selectedAffiliateId, page)}
                                />
                            </div>
                        </>
                    )}
                </section>
            )}
        </div>
    );
}
