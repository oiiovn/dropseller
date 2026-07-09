import React, { useCallback, useEffect, useState } from "react";
import { Link } from "react-router";
import { api } from "../../lib/api";
import { useAuth } from "../../context/AuthContext";
import TablePagination from "../../../shared/components/TablePagination";
import { CONSULTATION_STATUS_LABELS, statusBadgeClass } from "../../lib/orderStatuses";

const SEARCH_DEBOUNCE_MS = 300;

export default function CustomersListPage() {
    const { isCollaborator } = useAuth();
    const showAffiliateFields = !isCollaborator;

    const [rows, setRows] = useState([]);
    const [affiliates, setAffiliates] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");
    const [meta, setMeta] = useState({ current_page: 1, last_page: 1, total: 0 });

    const [search, setSearch] = useState("");
    const [debouncedSearch, setDebouncedSearch] = useState("");
    const [consultationStatus, setConsultationStatus] = useState("");
    const [prefecture, setPrefecture] = useState("");
    const [affiliateId, setAffiliateId] = useState("");

    useEffect(() => {
        const timer = setTimeout(() => setDebouncedSearch(search), SEARCH_DEBOUNCE_MS);
        return () => clearTimeout(timer);
    }, [search]);

    useEffect(() => {
        if (!showAffiliateFields) {
            return;
        }

        api.get("/affiliates", { params: { per_page: 200 } })
            .then((response) => setAffiliates(response.data?.data ?? response.data ?? []))
            .catch(() => setAffiliates([]));
    }, [showAffiliateFields]);

    const buildParams = useCallback(
        (page = 1) => {
            const params = { page, per_page: 15 };
            if (debouncedSearch.trim()) {
                params.search = debouncedSearch.trim();
            }
            if (consultationStatus) {
                params.consultation_status = consultationStatus;
            }
            if (prefecture.trim()) {
                params.prefecture = prefecture.trim();
            }
            if (affiliateId) {
                params.affiliate_id = affiliateId;
            }
            return params;
        },
        [debouncedSearch, consultationStatus, prefecture, affiliateId]
    );

    const load = useCallback(
        (page = 1) => {
            setLoading(true);
            setError("");

            api.get("/customers", { params: buildParams(page) })
                .then((response) => {
                    setRows(response.data?.data ?? []);
                    setMeta({
                        current_page: response.data?.current_page ?? 1,
                        last_page: response.data?.last_page ?? 1,
                        total: response.data?.total ?? 0,
                    });
                })
                .catch(() => setError("Không tải được dữ liệu."))
                .finally(() => setLoading(false));
        },
        [buildParams]
    );

    useEffect(() => {
        load(1);
    }, [load]);

    const clearFilters = () => {
        setSearch("");
        setConsultationStatus("");
        setPrefecture("");
        setAffiliateId("");
    };

    const hasActiveFilters = Boolean(search.trim() || consultationStatus || prefecture.trim() || affiliateId);

    return (
        <section className="data-table-page crm-card">
            <div className="data-table-page__header flex items-center justify-between gap-3">
                <h2 className="text-lg font-semibold">Quản lý khách hàng</h2>
                <Link to="/customers/new" className="crm-btn-primary">
                    Thêm khách hàng
                </Link>
            </div>

            <div className="data-table-page__filters crm-filter-bar">
                <input
                    className="crm-filter-input"
                    placeholder="Tìm tên, SĐT, email, tỉnh, mã CTV..."
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                />
                <select
                    className="crm-filter-select"
                    value={consultationStatus}
                    onChange={(event) => setConsultationStatus(event.target.value)}
                >
                    <option value="">Trạng thái tư vấn</option>
                    {Object.entries(CONSULTATION_STATUS_LABELS).map(([value, label]) => (
                        <option key={value} value={value}>
                            {label}
                        </option>
                    ))}
                </select>
                <input
                    className="crm-filter-select min-w-[140px]"
                    placeholder="Tỉnh/Prefecture"
                    value={prefecture}
                    onChange={(event) => setPrefecture(event.target.value)}
                />
                {showAffiliateFields && (
                    <select
                        className="crm-filter-select"
                        value={affiliateId}
                        onChange={(event) => setAffiliateId(event.target.value)}
                    >
                        <option value="">Cộng tác viên</option>
                        {affiliates.map((affiliate) => (
                            <option key={affiliate.id} value={affiliate.id}>
                                {affiliate.code} — {affiliate.full_name}
                            </option>
                        ))}
                    </select>
                )}
                {hasActiveFilters && (
                    <button type="button" className="crm-filter-clear" onClick={clearFilters}>
                        Xóa bộ lọc
                    </button>
                )}
            </div>

            <div className="data-table-page__messages">
                {loading && <p className="text-sm text-slate-500">Đang tải...</p>}
                {error && <p className="text-sm text-red-600">{error}</p>}
            </div>

            {!error && (
                <>
                    <div className="data-table-page__body">
                        <table className="crm-table min-w-full">
                            <thead>
                                <tr>
                                    <th>Khách hàng</th>
                                    <th>Điện thoại</th>
                                    <th>Tỉnh/Prefecture</th>
                                    {showAffiliateFields && <th>CTV</th>}
                                    <th>Tư vấn</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                {!loading && rows.length === 0 && (
                                    <tr>
                                        <td colSpan={showAffiliateFields ? 6 : 5} className="py-6 text-center text-sm text-slate-500">
                                            Không có khách hàng phù hợp bộ lọc.
                                        </td>
                                    </tr>
                                )}
                                {rows.map((row) => (
                                    <tr key={row.id}>
                                        <td>{row.full_name}</td>
                                        <td>{row.phone || "—"}</td>
                                        <td>{row.prefecture || "—"}</td>
                                        {showAffiliateFields && (
                                            <td>
                                                {row.affiliate ? (
                                                    <span>
                                                        {row.affiliate.code}
                                                        {row.affiliate.full_name ? ` — ${row.affiliate.full_name}` : ""}
                                                    </span>
                                                ) : (
                                                    "—"
                                                )}
                                            </td>
                                        )}
                                        <td>
                                            <span className={`crm-badge ${statusBadgeClass(row.consultation_status)}`}>
                                                {CONSULTATION_STATUS_LABELS[row.consultation_status] || row.consultation_status}
                                            </span>
                                        </td>
                                        <td>
                                            <Link
                                                to={`/customers/${row.id}/edit`}
                                                className="text-sm font-medium text-blue-600 hover:underline"
                                            >
                                                Chi tiết
                                            </Link>
                                        </td>
                                    </tr>
                                ))}
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
                </>
            )}
        </section>
    );
}
