import React, { useEffect, useMemo, useState } from "react";
import { Link } from "react-router";
import { adminApi, formatJpy } from "../lib/api";
import {
    ORDER_STATUS_LABELS,
    orderStatusAdminBadgeClass,
    paymentStatusLabel,
} from "../../crm/lib/orderStatuses";
import {
    COMMISSION_WALLET_STATUS_LABELS,
    commissionWalletStatusBadgeClass,
} from "../../crm/lib/commissionStatuses";
import DashboardTimeFilter from "../../shared/components/DashboardTimeFilter";

function currentMonthValue() {
    const now = new Date();
    const month = String(now.getMonth() + 1).padStart(2, "0");
    return `${now.getFullYear()}-${month}`;
}

function monthToRange(month) {
    if (!month) {
        return { dateFrom: "", dateTo: "" };
    }

    const [year, monthNum] = month.split("-").map(Number);
    const lastDay = new Date(year, monthNum, 0).getDate();

    return {
        dateFrom: `${month}-01`,
        dateTo: `${month}-${String(lastDay).padStart(2, "0")}`,
    };
}

function formatFilterLabel(dateFrom, dateTo) {
    if (!dateFrom && !dateTo) {
        return "Tất cả thời gian";
    }
    if (dateFrom && dateTo) {
        return `${dateFrom} → ${dateTo}`;
    }
    if (dateFrom) {
        return `Từ ${dateFrom}`;
    }
    return `Đến ${dateTo}`;
}

function formatDate(value) {
    if (!value) return "—";
    return String(value).slice(0, 10);
}

function MetricCard({ label, value, hint, tone = "blue", icon }) {
    return (
        <article className={`admin-dashboard-kpi admin-dashboard-kpi--${tone}`}>
            <div className="admin-dashboard-kpi__icon" aria-hidden="true">
                {icon}
            </div>
            <div className="admin-dashboard-kpi__body">
                <p className="admin-dashboard-kpi__label">{label}</p>
                <p className="admin-dashboard-kpi__value">{value}</p>
                {hint && <p className="admin-dashboard-kpi__hint">{hint}</p>}
            </div>
        </article>
    );
}

function MiniStat({ label, value, accent = false }) {
    return (
        <div className="admin-dashboard-mini-stat">
            <p className="admin-dashboard-mini-stat__label">{label}</p>
            <p className={`admin-dashboard-mini-stat__value ${accent ? "text-rose-600" : ""}`}>{value}</p>
        </div>
    );
}

export default function AdminDashboardPage() {
    const [data, setData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [month, setMonth] = useState(currentMonthValue());
    const [dateFrom, setDateFrom] = useState(() => monthToRange(currentMonthValue()).dateFrom);
    const [dateTo, setDateTo] = useState(() => monthToRange(currentMonthValue()).dateTo);

    const activeParams = useMemo(() => {
        const params = {};
        if (month) {
            params.month = month;
            return params;
        }
        if (dateFrom) params.date_from = dateFrom;
        if (dateTo) params.date_to = dateTo;
        return params;
    }, [month, dateFrom, dateTo]);

    useEffect(() => {
        setLoading(true);
        adminApi
            .get("/dashboard", { params: activeParams })
            .then((response) => setData(response.data))
            .finally(() => setLoading(false));
    }, [activeParams]);

    const handleMonthChange = (value) => {
        setMonth(value);
        if (value) {
            const range = monthToRange(value);
            setDateFrom(range.dateFrom);
            setDateTo(range.dateTo);
        }
    };

    const clearFilters = () => {
        setMonth("");
        setDateFrom("");
        setDateTo("");
    };

    const filterLabel = formatFilterLabel(data?.filter?.date_from, data?.filter?.date_to);
    const completionRate =
        data?.total_orders > 0 ? Math.round((data.completed_orders / data.total_orders) * 100) : 0;
    const maxTrendRevenue = Math.max(...(data?.revenue_trend || []).map((row) => row.revenue_jpy), 1);
    const pendingStatuses = Object.entries(data?.pending_orders_by_status || {}).sort(
        (a, b) => Number(b[1]) - Number(a[1])
    );
    const commission = data?.commission_overview;
    const commissionByStatus = Object.entries(commission?.by_wallet_status || {}).sort(
        (a, b) => Number(b[1]?.amount_jpy || 0) - Number(a[1]?.amount_jpy || 0)
    );
    const maxCommissionTrend = Math.max(...(commission?.trend || []).map((row) => row.commission_jpy), 1);

    return (
        <div className="admin-dashboard space-y-5">
            <section className="admin-dashboard-hero">
                <div>
                    <p className="admin-dashboard-hero__eyebrow">Dashboard tổng quan</p>
                    <h1 className="admin-dashboard-hero__title">Quản trị Himawari</h1>
                    <p className="admin-dashboard-hero__subtitle">
                        Theo dõi đơn hàng, doanh số, công nợ, hoa hồng CTV và hiệu quả cộng tác viên trên toàn hệ thống.
                    </p>
                </div>
                <div className="admin-dashboard-hero__actions">
                    <Link to="/orders" className="admin-btn-primary">
                        Quản lý đơn hàng
                    </Link>
                    <Link to="/payments" className="admin-btn-secondary">
                        Thanh toán & quyết toán HH
                    </Link>
                    <Link to="/affiliates" className="admin-btn-secondary">
                        Danh sách CTV
                    </Link>
                </div>
            </section>

            <DashboardTimeFilter
                month={month}
                dateFrom={dateFrom}
                dateTo={dateTo}
                loading={loading}
                filterLabel={filterLabel}
                onMonthChange={handleMonthChange}
                onDateFromChange={(value) => {
                    setMonth("");
                    setDateFrom(value);
                }}
                onDateToChange={(value) => {
                    setMonth("");
                    setDateTo(value);
                }}
                onClear={clearFilters}
                clearButtonClassName="admin-btn-secondary h-9 px-3 py-0 text-sm"
            />

            <section className="admin-dashboard-kpi-grid">
                <MetricCard
                    icon="📦"
                    tone="blue"
                    label="Tổng đơn"
                    value={loading ? "..." : Number(data?.total_orders || 0).toLocaleString("vi-VN")}
                    hint={`${data?.completed_orders ?? 0} hoàn thành · ${completionRate}%`}
                />
                <MetricCard
                    icon="💴"
                    tone="emerald"
                    label="Doanh số"
                    value={loading ? "..." : formatJpy(data?.total_revenue_jpy || 0)}
                    hint={`Đã thu ${formatJpy(data?.collected_revenue_jpy || 0)}`}
                />
                <MetricCard
                    icon="📊"
                    tone="amber"
                    label="Công nợ còn lại"
                    value={loading ? "..." : formatJpy(data?.outstanding_debt_jpy || 0)}
                    hint={`${data?.overdue_debt_count ?? 0} khoản quá hạn`}
                />
                <MetricCard
                    icon="⏳"
                    tone="sky"
                    label="Đơn chờ xử lý"
                    value={loading ? "..." : Number(data?.pending_orders_count || 0).toLocaleString("vi-VN")}
                    hint={`${data?.pending_payments_count ?? 0} thanh toán chờ kế toán`}
                />
                <MetricCard
                    icon="🏆"
                    tone="rose"
                    label="CTV hiệu quả"
                    value={loading ? "..." : Number(data?.effective_affiliates_count || 0).toLocaleString("vi-VN")}
                    hint={`${data?.active_affiliates ?? 0}/${data?.total_affiliates ?? 0} CTV đang hoạt động`}
                />
            </section>

            <section className="admin-dashboard-mini-grid admin-card">
                <MiniStat label="Đơn hoàn thành" value={loading ? "..." : data?.completed_orders ?? 0} />
                <MiniStat label="Đơn hủy" value={loading ? "..." : data?.cancelled_orders ?? 0} />
                <MiniStat label="Lợi nhuận gộp" value={loading ? "..." : formatJpy(data?.total_profit_jpy || 0)} />
                <MiniStat
                    label="Nợ quá hạn"
                    value={loading ? "..." : formatJpy(data?.overdue_debt_amount_jpy || 0)}
                    accent={Number(data?.overdue_debt_amount_jpy) > 0}
                />
                <MiniStat label="Khách hàng" value={loading ? "..." : data?.total_customers ?? 0} />
                <MiniStat label="Người dùng hệ thống" value={loading ? "..." : data?.total_users ?? 0} />
            </section>

            <section className="admin-dashboard-section">
                <div className="admin-dashboard-section__head">
                    <div>
                        <h2 className="admin-dashboard-section__title">Tổng quan hoa hồng CTV</h2>
                        <p className="admin-dashboard-section__subtitle">
                            Ví tích luỹ, phát sinh theo kỳ và phiếu quyết toán chờ kế toán
                        </p>
                    </div>
                    <Link to="/payments" className="text-sm font-medium text-blue-600 hover:underline">
                        Quản lý quyết toán →
                    </Link>
                </div>

                <div className="admin-dashboard-kpi-grid admin-dashboard-kpi-grid--4">
                    <MetricCard
                        icon="💼"
                        tone="blue"
                        label="Chưa quyết toán"
                        value={loading ? "..." : formatJpy(commission?.wallet_unpaid_balance_jpy || 0)}
                        hint="Tổng số dư ví CTV chưa chi"
                    />
                    <MetricCard
                        icon="✅"
                        tone="emerald"
                        label="Đã quyết toán"
                        value={loading ? "..." : formatJpy(commission?.wallet_paid_balance_jpy || 0)}
                        hint={`Tổng tích luỹ ${formatJpy(commission?.wallet_total_commission_jpy || 0)}`}
                    />
                    <MetricCard
                        icon="📈"
                        tone="blue"
                        label="Hoa hồng phát sinh (kỳ)"
                        value={loading ? "..." : formatJpy(commission?.period_commission_jpy || 0)}
                        hint={`${commission?.period_commission_count ?? 0} giao dịch ghi ví`}
                    />
                    <MetricCard
                        icon="🧾"
                        tone="amber"
                        label="Phiếu QT chờ duyệt"
                        value={loading ? "..." : Number(commission?.pending_settlements_count || 0).toLocaleString("vi-VN")}
                        hint={formatJpy(commission?.pending_settlements_amount_jpy || 0)}
                    />
                </div>

                <div className="admin-dashboard-split mt-4">
                    <article className="admin-card admin-dashboard-panel">
                        <div className="admin-dashboard-panel__head">
                            <h3>Xu hướng hoa hồng 6 tháng</h3>
                            <p>Phát sinh và đã quyết toán theo tháng</p>
                        </div>
                        <div className="admin-dashboard-trend">
                            {(commission?.trend || []).map((row) => (
                                <div key={row.period_key} className="admin-dashboard-trend__item">
                                    <div className="admin-dashboard-trend__bar-wrap admin-dashboard-trend__bar-wrap--dual">
                                        <div
                                            className="admin-dashboard-trend__bar admin-dashboard-trend__bar--commission"
                                            style={{
                                                height: `${Math.max((row.commission_jpy / maxCommissionTrend) * 100, 6)}%`,
                                            }}
                                            title={`Phát sinh ${formatJpy(row.commission_jpy)}`}
                                        />
                                        <div
                                            className="admin-dashboard-trend__bar admin-dashboard-trend__bar--settled"
                                            style={{
                                                height: `${Math.max((row.settled_jpy / maxCommissionTrend) * 100, 4)}%`,
                                            }}
                                            title={`Đã QT ${formatJpy(row.settled_jpy)}`}
                                        />
                                    </div>
                                    <p className="admin-dashboard-trend__label">{row.label}</p>
                                    <p className="admin-dashboard-trend__meta">{row.commission_count} HH</p>
                                </div>
                            ))}
                        </div>
                        <div className="mt-3 flex flex-wrap gap-4 text-xs text-slate-500">
                            <span className="flex items-center gap-1.5">
                                <span className="inline-block h-2.5 w-2.5 rounded-sm bg-blue-500" /> Phát sinh
                            </span>
                            <span className="flex items-center gap-1.5">
                                <span className="inline-block h-2.5 w-2.5 rounded-sm bg-emerald-500" /> Đã quyết toán
                            </span>
                        </div>
                    </article>

                    <article className="admin-card admin-dashboard-panel">
                        <div className="admin-dashboard-panel__head">
                            <h3>Trạng thái hoa hồng (kỳ)</h3>
                            <p>Phân bổ theo vòng đời ví hoa hồng</p>
                        </div>
                        <div className="admin-dashboard-pipeline">
                            {commissionByStatus.length === 0 && !loading && (
                                <p className="text-sm text-slate-500">Chưa có hoa hồng trong kỳ đã chọn.</p>
                            )}
                            {commissionByStatus.map(([status, stats]) => (
                                <div key={status} className="admin-dashboard-pipeline__row">
                                    <span className={`admin-badge ${commissionWalletStatusBadgeClass(status)}`}>
                                        {COMMISSION_WALLET_STATUS_LABELS[status] || status}
                                    </span>
                                    <div className="admin-dashboard-pipeline__track">
                                        <div
                                            className="admin-dashboard-pipeline__fill admin-dashboard-pipeline__fill--commission"
                                            style={{
                                                width: `${Math.max(
                                                    (Number(stats.amount_jpy) /
                                                        Math.max(commission?.period_commission_jpy || 1, 1)) *
                                                        100,
                                                    4
                                                )}%`,
                                            }}
                                        />
                                    </div>
                                    <span className="admin-dashboard-pipeline__count">
                                        {stats.count} · {formatJpy(stats.amount_jpy)}
                                    </span>
                                </div>
                            ))}
                        </div>
                    </article>
                </div>

                <div className="admin-dashboard-split mt-4">
                    <article className="admin-card admin-dashboard-panel">
                        <div className="admin-dashboard-panel__head">
                            <h3>Top CTV theo hoa hồng</h3>
                            <p>Xếp theo hoa hồng phát sinh trong kỳ</p>
                        </div>
                        <div className="admin-dashboard-table-wrap">
                            <table className="admin-table min-w-full">
                                <thead>
                                    <tr>
                                        <th>CTV</th>
                                        <th>Số HH</th>
                                        <th>Chưa QT</th>
                                        <th className="text-right">Phát sinh kỳ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {(commission?.top_affiliates || []).map((item, index) => (
                                        <tr key={item.id}>
                                            <td>
                                                <div className="flex items-center gap-2">
                                                    <span className="admin-dashboard-rank">{index + 1}</span>
                                                    <div>
                                                        <p className="font-medium">{item.full_name || "—"}</p>
                                                        <p className="text-xs text-slate-500">{item.code}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>{item.commission_count}</td>
                                            <td className="text-amber-700">{formatJpy(item.unpaid_balance_jpy)}</td>
                                            <td className="text-right font-semibold text-blue-700">
                                                {formatJpy(item.commission_amount_jpy)}
                                            </td>
                                        </tr>
                                    ))}
                                    {!loading && (commission?.top_affiliates || []).length === 0 && (
                                        <tr>
                                            <td colSpan={4} className="py-6 text-center text-slate-500">
                                                Chưa có hoa hồng CTV trong kỳ này.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </article>

                    <article className="admin-card admin-dashboard-panel">
                        <div className="admin-dashboard-panel__head">
                            <h3>Hoa hồng phát sinh gần đây</h3>
                            <p>Ghi nhận mới nhất trong kỳ đã chọn</p>
                        </div>
                        <div className="admin-dashboard-table-wrap">
                            <table className="admin-table min-w-full">
                                <thead>
                                    <tr>
                                        <th>Đơn / CTV</th>
                                        <th>Rule</th>
                                        <th>Trạng thái</th>
                                        <th className="text-right">Hoa hồng</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {(commission?.recent_commissions || []).map((item) => (
                                        <tr key={item.id}>
                                            <td>
                                                {item.order_id ? (
                                                    <Link
                                                        to={`/orders/${item.order_id}`}
                                                        className="font-medium text-blue-600 hover:underline"
                                                    >
                                                        {item.order_code || `#${item.order_id}`}
                                                    </Link>
                                                ) : (
                                                    <span>{item.order_code || "—"}</span>
                                                )}
                                                <p className="text-xs text-slate-500">
                                                    {item.affiliate_code} · {item.affiliate_name}
                                                </p>
                                                <p className="text-xs text-slate-400">{formatDate(item.credited_at)}</p>
                                            </td>
                                            <td className="text-sm text-slate-600">{item.applied_rule_name || "—"}</td>
                                            <td>
                                                <span className={`admin-badge ${commissionWalletStatusBadgeClass(item.wallet_status)}`}>
                                                    {COMMISSION_WALLET_STATUS_LABELS[item.wallet_status] || item.wallet_status}
                                                </span>
                                            </td>
                                            <td className="text-right font-semibold text-blue-700">
                                                {formatJpy(item.commission_amount_jpy)}
                                            </td>
                                        </tr>
                                    ))}
                                    {!loading && (commission?.recent_commissions || []).length === 0 && (
                                        <tr>
                                            <td colSpan={4} className="py-6 text-center text-slate-500">
                                                Chưa có hoa hồng phát sinh.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </article>
                </div>
            </section>

            <section className="admin-dashboard-split">
                <article className="admin-card admin-dashboard-panel">
                    <div className="admin-dashboard-panel__head">
                        <h3>Xu hướng 6 tháng</h3>
                        <p>Doanh số và số đơn theo tháng</p>
                    </div>
                    <div className="admin-dashboard-trend">
                        {(data?.revenue_trend || []).map((row) => (
                            <div key={row.period_key} className="admin-dashboard-trend__item">
                                <div className="admin-dashboard-trend__bar-wrap">
                                    <div
                                        className="admin-dashboard-trend__bar"
                                        style={{ height: `${Math.max((row.revenue_jpy / maxTrendRevenue) * 100, 6)}%` }}
                                        title={formatJpy(row.revenue_jpy)}
                                    />
                                </div>
                                <p className="admin-dashboard-trend__label">{row.label}</p>
                                <p className="admin-dashboard-trend__meta">{row.orders} đơn</p>
                            </div>
                        ))}
                        {!loading && (data?.revenue_trend || []).length === 0 && (
                            <p className="text-sm text-slate-500">Chưa có dữ liệu xu hướng.</p>
                        )}
                    </div>
                </article>

                <article className="admin-card admin-dashboard-panel">
                    <div className="admin-dashboard-panel__head">
                        <h3>Đơn chờ xử lý theo trạng thái</h3>
                        <p>{data?.pending_orders_count ?? 0} đơn đang trong luồng xử lý</p>
                    </div>
                    <div className="admin-dashboard-pipeline">
                        {pendingStatuses.length === 0 && !loading && (
                            <p className="text-sm text-slate-500">Không có đơn chờ xử lý trong kỳ đã chọn.</p>
                        )}
                        {pendingStatuses.map(([status, total]) => (
                            <div key={status} className="admin-dashboard-pipeline__row">
                                <span className={orderStatusAdminBadgeClass(status)}>
                                    {ORDER_STATUS_LABELS[status] || status}
                                </span>
                                <div className="admin-dashboard-pipeline__track">
                                    <div
                                        className="admin-dashboard-pipeline__fill"
                                        style={{
                                            width: `${Math.max(
                                                (Number(total) / Math.max(data?.pending_orders_count || 1, 1)) * 100,
                                                4
                                            )}%`,
                                        }}
                                    />
                                </div>
                                <span className="admin-dashboard-pipeline__count">{total}</span>
                            </div>
                        ))}
                    </div>
                </article>
            </section>

            <section className="admin-dashboard-split">
                <article className="admin-card admin-dashboard-panel">
                    <div className="admin-dashboard-panel__head">
                        <h3>Top CTV hiệu quả</h3>
                        <p>Xếp theo doanh số trong kỳ đã chọn</p>
                    </div>
                    <div className="admin-dashboard-table-wrap">
                        <table className="admin-table min-w-full">
                            <thead>
                                <tr>
                                    <th>CTV</th>
                                    <th>Đơn</th>
                                    <th>Hoàn thành</th>
                                    <th>Tỉ lệ</th>
                                    <th className="text-right">Doanh số</th>
                                </tr>
                            </thead>
                            <tbody>
                                {(data?.top_affiliates || []).map((item, index) => (
                                    <tr key={item.id}>
                                        <td>
                                            <div className="flex items-center gap-2">
                                                <span className="admin-dashboard-rank">{index + 1}</span>
                                                <div>
                                                    <p className="font-medium">{item.full_name || "—"}</p>
                                                    <p className="text-xs text-slate-500">{item.code}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{item.total_orders}</td>
                                        <td>{item.successful_orders}</td>
                                        <td>
                                            <span className="admin-dashboard-rate">{item.success_rate}%</span>
                                        </td>
                                        <td className="text-right font-semibold text-blue-700">
                                            {formatJpy(item.gross_sales_jpy)}
                                        </td>
                                    </tr>
                                ))}
                                {!loading && (data?.top_affiliates || []).length === 0 && (
                                    <tr>
                                        <td colSpan={5} className="py-6 text-center text-slate-500">
                                            Chưa có dữ liệu CTV trong kỳ này.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </article>

                <article className="admin-card admin-dashboard-panel">
                    <div className="admin-dashboard-panel__head">
                        <h3>Đơn cần xử lý gần đây</h3>
                        <Link to="/orders" className="text-sm font-medium text-blue-600 hover:underline">
                            Xem tất cả →
                        </Link>
                    </div>
                    <div className="admin-dashboard-table-wrap">
                        <table className="admin-table min-w-full">
                            <thead>
                                <tr>
                                    <th>Mã đơn</th>
                                    <th>Khách / CTV</th>
                                    <th>Trạng thái</th>
                                    <th className="text-right">Giá trị</th>
                                </tr>
                            </thead>
                            <tbody>
                                {(data?.recent_pending_orders || []).map((order) => (
                                    <tr key={order.id}>
                                        <td>
                                            <Link
                                                to={`/orders/${order.id}`}
                                                className="font-medium text-blue-600 hover:underline"
                                            >
                                                {order.order_code}
                                            </Link>
                                            <p className="text-xs text-slate-400">{formatDate(order.created_at)}</p>
                                        </td>
                                        <td>
                                            <p>{order.customer_name || "—"}</p>
                                            <p className="text-xs text-slate-500">
                                                {order.affiliate_code || "—"} · {order.affiliate_name || "—"}
                                            </p>
                                        </td>
                                        <td>
                                            <span className={orderStatusAdminBadgeClass(order.order_status)}>
                                                {ORDER_STATUS_LABELS[order.order_status] || order.order_status}
                                            </span>
                                            <p className="mt-1 text-xs text-slate-500">
                                                {paymentStatusLabel(order.payment_status)}
                                            </p>
                                        </td>
                                        <td className="text-right font-medium">{formatJpy(order.total_amount_jpy)}</td>
                                    </tr>
                                ))}
                                {!loading && (data?.recent_pending_orders || []).length === 0 && (
                                    <tr>
                                        <td colSpan={4} className="py-6 text-center text-slate-500">
                                            Không có đơn chờ xử lý.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </article>
            </section>

            <section className="admin-dashboard-split">
                <article className="admin-card admin-dashboard-panel">
                    <div className="admin-dashboard-panel__head">
                        <h3>Cảnh báo công nợ</h3>
                        <p>{data?.overdue_debt_count ?? 0} khoản quá hạn</p>
                    </div>
                    <ul className="admin-dashboard-alerts">
                        {(data?.latest_alerts || []).map((alert) => (
                            <li key={alert.id} className="admin-dashboard-alert">
                                <p className="admin-dashboard-alert__title">{alert.title}</p>
                                <p className="admin-dashboard-alert__message">{alert.message}</p>
                            </li>
                        ))}
                        {!loading && (data?.latest_alerts || []).length === 0 && (
                            <li className="text-sm text-slate-500">Không có cảnh báo công nợ quá hạn.</li>
                        )}
                    </ul>
                </article>

                <article className="admin-card admin-dashboard-panel">
                    <div className="admin-dashboard-panel__head">
                        <h3>Phân bổ vai trò</h3>
                        <p>Người dùng theo nhóm quyền CRM</p>
                    </div>
                    <div className="admin-dashboard-roles">
                        {(data?.roles || []).map((role) => (
                            <div key={role.id} className="admin-dashboard-role">
                                <p className="admin-dashboard-role__name">{role.display_name}</p>
                                <p className="admin-dashboard-role__count">{role.users_count}</p>
                            </div>
                        ))}
                    </div>
                </article>
            </section>
        </div>
    );
}
