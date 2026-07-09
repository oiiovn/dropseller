import React, { useEffect, useState } from "react";
import { Link } from "react-router";
import { adminApi, formatJpy } from "../../lib/api";
import TablePagination from "../../../shared/components/TablePagination";
import {
    ORDER_STATUS_LABELS,
    orderStatusAdminBadgeClass,
    paymentStatusLabel,
    statusBadgeClass,
} from "../../../crm/lib/orderStatuses";

export default function OrderListPage() {
    const [rows, setRows] = useState([]);
    const [affiliates, setAffiliates] = useState([]);
    const [loading, setLoading] = useState(true);
    const [filters, setFilters] = useState({
        search: "",
        affiliate_id: "",
        order_status: "",
        payment_status: "",
        trashed: false,
    });
    const [meta, setMeta] = useState({ current_page: 1, last_page: 1, total: 0 });

    const load = (page = 1) => {
        setLoading(true);
        const params = { page, per_page: 20 };
        if (filters.search) params.search = filters.search;
        if (filters.affiliate_id) params.affiliate_id = filters.affiliate_id;
        if (filters.order_status) params.order_status = filters.order_status;
        if (filters.payment_status) params.payment_status = filters.payment_status;
        if (filters.trashed) params.trashed = 1;

        adminApi
            .get("/orders", { params })
            .then((response) => {
                setRows(response.data?.data ?? []);
                setMeta({
                    current_page: response.data?.current_page ?? 1,
                    last_page: response.data?.last_page ?? 1,
                    total: response.data?.total ?? 0,
                });
            })
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        adminApi.get("/affiliates", { params: { per_page: 200 } }).then((response) => {
            setAffiliates(response.data?.data ?? response.data ?? []);
        });
    }, []);

    useEffect(() => {
        load(1);
    }, [filters.trashed]);

    const handleSearch = (event) => {
        event.preventDefault();
        load(1);
    };

    return (
        <section className="data-table-page admin-card">
            <div className="data-table-page__header flex flex-wrap items-center justify-between gap-3">
                <div>
                    <h2 className="text-lg font-semibold">Quản lý đơn hàng CTV</h2>
                    <p className="text-sm text-slate-500">Toàn bộ đơn hàng — sửa, xóa mềm và theo dõi dấu vết</p>
                </div>
                <label className="inline-flex items-center gap-2 text-sm text-slate-600">
                    <input
                        type="checkbox"
                        checked={filters.trashed}
                        onChange={(event) => setFilters((current) => ({ ...current, trashed: event.target.checked }))}
                    />
                    Chỉ đơn đã xóa
                </label>
            </div>

            <form className="data-table-page__filters mb-0 grid gap-3 md:grid-cols-5" onSubmit={handleSearch}>
                <input
                    className="rounded-lg border border-slate-200 px-3 py-2 text-sm md:col-span-2"
                    placeholder="Tìm mã đơn, khách, CTV..."
                    value={filters.search}
                    onChange={(event) => setFilters((current) => ({ ...current, search: event.target.value }))}
                />
                <select
                    className="rounded-lg border border-slate-200 px-3 py-2 text-sm"
                    value={filters.affiliate_id}
                    onChange={(event) => setFilters((current) => ({ ...current, affiliate_id: event.target.value }))}
                >
                    <option value="">Tất cả CTV</option>
                    {affiliates.map((affiliate) => (
                        <option key={affiliate.id} value={affiliate.id}>
                            {affiliate.code} — {affiliate.full_name}
                        </option>
                    ))}
                </select>
                <select
                    className="rounded-lg border border-slate-200 px-3 py-2 text-sm"
                    value={filters.order_status}
                    onChange={(event) => setFilters((current) => ({ ...current, order_status: event.target.value }))}
                >
                    <option value="">Trạng thái đơn</option>
                    {Object.entries(ORDER_STATUS_LABELS).map(([value, label]) => (
                        <option key={value} value={value}>
                            {label}
                        </option>
                    ))}
                </select>
                <button type="submit" className="admin-btn-primary">
                    Lọc
                </button>
            </form>

            {loading ? (
                <p className="text-sm text-slate-500">Đang tải...</p>
            ) : (
                <>
                    <div className="data-table-page__body">
                        <table className="admin-table min-w-full">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>CTV</th>
                                <th>Khách hàng</th>
                                <th>Trạng thái</th>
                                <th>Thanh toán</th>
                                <th>Tổng</th>
                                <th>Ngày tạo</th>
                                <th />
                            </tr>
                        </thead>
                        <tbody>
                            {rows.length === 0 && (
                                <tr>
                                    <td colSpan={8} className="py-6 text-center text-slate-500">
                                        Không có đơn hàng.
                                    </td>
                                </tr>
                            )}
                            {rows.map((row) => (
                                <tr key={row.id} className={row.deleted_at ? "bg-red-50/60" : ""}>
                                    <td className="font-medium">{row.order_code}</td>
                                    <td>{row.affiliate?.code || "—"}</td>
                                    <td>{row.customer?.full_name || "—"}</td>
                                    <td>
                                        <span className={orderStatusAdminBadgeClass(row.order_status)}>
                                            {ORDER_STATUS_LABELS[row.order_status] || row.order_status}
                                        </span>
                                    </td>
                                    <td>
                                        <span className={`admin-badge ${statusBadgeClass(row.payment_status)}`}>
                                            {paymentStatusLabel(row.payment_status)}
                                        </span>
                                    </td>
                                    <td>{formatJpy(row.total_amount_jpy)}</td>
                                    <td>{String(row.created_at || "").slice(0, 10)}</td>
                                    <td>
                                        <Link to={`/orders/${row.id}`} className="text-sm text-blue-600 hover:underline">
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
                            buttonClassName="admin-btn-secondary px-3 py-1.5 text-xs"
                        />
                    </div>
                </>
            )}
        </section>
    );
}
