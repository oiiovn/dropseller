import React, { useEffect, useState } from "react";
import { Link } from "react-router";
import { api, formatJpy } from "../../lib/api";
import { useAuth } from "../../context/AuthContext";
import TablePagination from "../../../shared/components/TablePagination";
import {
    ORDER_STATUS_LABELS,
    PAYMENT_STATUS_LABELS,
    SHIPPING_MODE_LABELS,
    orderStatusCrmBadgeClass,
    paymentStatusLabel,
    shippingModeLabel,
    shippingModeBadgeClass,
    statusBadgeClass,
} from "../../lib/orderStatuses";

const ORDER_PAYMENT_STATUSES = ["unpaid", "partial", "paid", "refunded"];
const SHIPPING_MODES = Object.keys(SHIPPING_MODE_LABELS);
const SEARCH_DEBOUNCE_MS = 300;

export default function OrdersListPage() {
    const { isPackaging, isCollaborator } = useAuth();
    const [rows, setRows] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");
    const [deleteError, setDeleteError] = useState("");
    const [deletingId, setDeletingId] = useState(null);
    const [search, setSearch] = useState("");
    const [debouncedSearch, setDebouncedSearch] = useState("");
    const [orderStatus, setOrderStatus] = useState("");
    const [paymentStatus, setPaymentStatus] = useState("");
    const [shippingMode, setShippingMode] = useState("");
    const [meta, setMeta] = useState({ current_page: 1, last_page: 1, total: 0 });

    const load = (
        page = 1,
        activeFilters = {
            search: debouncedSearch,
            order_status: orderStatus,
            payment_status: paymentStatus,
            shipping_mode: shippingMode,
        }
    ) => {
        setLoading(true);
        setError("");

        const params = { page, per_page: 15 };
        if (activeFilters.search.trim()) params.search = activeFilters.search.trim();
        if (activeFilters.order_status) params.order_status = activeFilters.order_status;
        if (activeFilters.payment_status) params.payment_status = activeFilters.payment_status;
        if (activeFilters.shipping_mode) params.shipping_mode = activeFilters.shipping_mode;

        api.get("/orders", { params })
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
    };

    useEffect(() => {
        const timer = setTimeout(() => setDebouncedSearch(search), SEARCH_DEBOUNCE_MS);
        return () => clearTimeout(timer);
    }, [search]);

    useEffect(() => {
        load(1, {
            search: debouncedSearch,
            order_status: orderStatus,
            payment_status: paymentStatus,
            shipping_mode: shippingMode,
        });
    }, [debouncedSearch, orderStatus, paymentStatus, shippingMode]);

    const handleReset = () => {
        setSearch("");
        setDebouncedSearch("");
        setOrderStatus("");
        setPaymentStatus("");
        setShippingMode("");
    };

    const hasActiveFilters = search || orderStatus || paymentStatus || shippingMode;

    const handleDelete = async (row) => {
        if (!window.confirm(`Xóa đơn ${row.order_code}? Hành động này không thể hoàn tác.`)) {
            return;
        }

        setDeletingId(row.id);
        setDeleteError("");
        try {
            await api.delete(`/orders/${row.id}`);
            load(meta.current_page);
        } catch (requestError) {
            const message =
                requestError.response?.data?.message ||
                requestError.response?.data?.errors?.order?.[0] ||
                "Không xóa được đơn hàng.";
            setDeleteError(message);
        } finally {
            setDeletingId(null);
        }
    };

    return (
        <section className="data-table-page crm-card">
            <div className="data-table-page__header flex flex-wrap items-center justify-between gap-3">
                <h2 className="text-lg font-semibold">
                    {isPackaging ? "Đơn đóng gói vận chuyển" : "Quản lý đơn hàng"}
                </h2>
                {!isPackaging && (
                    <Link to="/orders/new" className="crm-btn-primary">
                        Tạo đơn hàng
                    </Link>
                )}
            </div>

            <div className="data-table-page__filters flex flex-nowrap items-center gap-2 overflow-x-auto">
                <input
                    className="min-w-[160px] flex-[2] rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    placeholder="Tìm mã đơn, khách hàng..."
                    value={search}
                    onChange={(event) => setSearch(event.target.value)}
                />
                <select
                    className="min-w-[130px] flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    value={orderStatus}
                    onChange={(event) => setOrderStatus(event.target.value)}
                >
                    <option value="">Trạng thái đơn</option>
                    {Object.entries(ORDER_STATUS_LABELS).map(([value, label]) => (
                        <option key={value} value={value}>
                            {label}
                        </option>
                    ))}
                </select>
                <select
                    className="min-w-[130px] flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    value={paymentStatus}
                    onChange={(event) => setPaymentStatus(event.target.value)}
                >
                    <option value="">Thanh toán</option>
                    {ORDER_PAYMENT_STATUSES.map((value) => (
                        <option key={value} value={value}>
                            {PAYMENT_STATUS_LABELS[value]}
                        </option>
                    ))}
                </select>
                <select
                    className="min-w-[130px] flex-1 rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100"
                    value={shippingMode}
                    onChange={(event) => setShippingMode(event.target.value)}
                >
                    <option value="">Vận chuyển</option>
                    {SHIPPING_MODES.map((value) => (
                        <option key={value} value={value}>
                            {SHIPPING_MODE_LABELS[value]}
                        </option>
                    ))}
                </select>
                {hasActiveFilters && (
                    <button type="button" className="crm-btn-secondary shrink-0 whitespace-nowrap" onClick={handleReset}>
                        Xóa bộ lọc
                    </button>
                )}
            </div>

            <div className="data-table-page__messages">
                {loading && <p className="text-sm text-slate-500">Đang tải...</p>}
                {error && <p className="text-sm text-red-600">{error}</p>}
                {deleteError && <p className="text-sm text-red-600">{deleteError}</p>}
            </div>

            {!error && (
                <>
                    <div className="data-table-page__body">
                        <table className="crm-table min-w-full">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Trạng thái đơn</th>
                                <th>Thanh toán</th>
                                <th>Vận chuyển</th>
                                <th>Tổng tiền</th>
                                <th>Đã thu</th>
                                <th>Còn lại</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            {rows.length === 0 && (
                                <tr>
                                    <td colSpan={9} className="py-6 text-center text-sm text-slate-500">
                                        Chưa có dữ liệu
                                    </td>
                                </tr>
                            )}
                            {rows.map((row) => (
                                <tr key={row.id}>
                                    <td className="font-medium">{row.order_code}</td>
                                    <td>{row.customer?.full_name || "—"}</td>
                                    <td>
                                        <span className={orderStatusCrmBadgeClass(row.order_status)}>
                                            {ORDER_STATUS_LABELS[row.order_status] || row.order_status}
                                        </span>
                                    </td>
                                    <td>
                                        <span className={`crm-badge ${statusBadgeClass(row.payment_status)}`}>
                                            {paymentStatusLabel(row.payment_status)}
                                        </span>
                                    </td>
                                    <td>
                                        {row.shipping_mode ? (
                                            <span className={`crm-badge ${shippingModeBadgeClass(row.shipping_mode)}`}>
                                                {shippingModeLabel(row.shipping_mode)}
                                            </span>
                                        ) : (
                                            "—"
                                        )}
                                    </td>
                                    <td>{formatJpy(row.total_amount_jpy)}</td>
                                    <td>{formatJpy(row.debt?.total_paid_jpy ?? 0)}</td>
                                    <td>{formatJpy(row.debt?.outstanding_jpy ?? row.total_amount_jpy ?? 0)}</td>
                                    <td>
                                        <div className="flex flex-wrap items-center gap-2">
                                            <Link
                                                to={`/orders/${row.id}`}
                                                className="text-sm font-medium text-blue-600 hover:underline"
                                            >
                                                Chi tiết
                                            </Link>
                                            {isCollaborator && row.can_delete && (
                                                <button
                                                    type="button"
                                                    className="text-sm font-medium text-red-600 hover:underline disabled:opacity-50"
                                                    disabled={deletingId === row.id}
                                                    onClick={() => handleDelete(row)}
                                                >
                                                    {deletingId === row.id ? "Đang xóa..." : "Xóa"}
                                                </button>
                                            )}
                                        </div>
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
                            onPageChange={(page) =>
                                load(page, {
                                    search: debouncedSearch,
                                    order_status: orderStatus,
                                    payment_status: paymentStatus,
                                    shipping_mode: shippingMode,
                                })
                            }
                        />
                    </div>
                </>
            )}
        </section>
    );
}
