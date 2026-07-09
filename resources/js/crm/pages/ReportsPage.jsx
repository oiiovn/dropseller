import React, { useEffect, useMemo, useState } from "react";
import { api, formatJpy } from "../lib/api";

export default function ReportsPage({ view = "overview", apiClient = api }) {
    const [granularity, setGranularity] = useState("month");
    const [report, setReport] = useState(null);
    const [affiliateReport, setAffiliateReport] = useState(null);
    const [alerts, setAlerts] = useState([]);

    useEffect(() => {
        Promise.all([
            apiClient.get("/reports/advanced", { params: { granularity, periods: 6 } }),
            apiClient.get("/reports/affiliates", { params: { granularity, top: 10 } }),
            apiClient.get("/alerts/overdue-debts", { params: { limit: 8 } }),
        ]).then(([advanced, affiliate, overdue]) => {
            setReport(advanced.data);
            setAffiliateReport(affiliate.data);
            setAlerts(overdue.data?.alerts || []);
        });
    }, [granularity, apiClient]);

    const totals = useMemo(() => {
        const series = report?.series || [];
        return series.reduce(
            (acc, row) => {
                acc.revenue += Number(row.revenue_jpy || 0);
                acc.profit += Number(row.profit_jpy || 0);
                acc.collected += Number(row.collected_jpy || 0);
                acc.orders += Number(row.orders || 0);
                return acc;
            },
            { revenue: 0, profit: 0, collected: 0, orders: 0 }
        );
    }, [report]);

    const titles = {
        overview: "Báo cáo tổng quan",
        revenue: "Báo cáo doanh thu",
        affiliates: "Báo cáo CTV",
    };

    const showSummary = view === "overview" || view === "revenue";
    const showSeries = view === "overview" || view === "revenue";
    const showAffiliates = view === "overview" || view === "affiliates";
    const showAlerts = view === "overview";

    return (
        <div className="flex min-h-0 flex-1 flex-col gap-4">
            {showSummary && (
                <section className="crm-card shrink-0">
                    <div className="mb-4 flex items-center justify-between">
                        <h2 className="text-lg font-semibold">{titles[view] || titles.overview}</h2>
                        <select
                            className="rounded-lg border border-slate-300 px-3 py-1 text-sm"
                            value={granularity}
                            onChange={(event) => setGranularity(event.target.value)}
                        >
                            <option value="month">Theo tháng</option>
                            <option value="week">Theo tuần</option>
                        </select>
                    </div>
                    <div className="grid gap-3 md:grid-cols-4">
                        <StatBox label="Tổng đơn" value={totals.orders} />
                        <StatBox label="Doanh thu" value={totals.revenue} isMoney />
                        <StatBox label="Lợi nhuận" value={totals.profit} isMoney />
                        <StatBox label="Đã thu" value={totals.collected} isMoney />
                    </div>
                </section>
            )}

            {view === "affiliates" && (
                <section className="crm-card shrink-0">
                    <div className="mb-4 flex items-center justify-between">
                        <h2 className="text-lg font-semibold">{titles.affiliates}</h2>
                        <select
                            className="rounded-lg border border-slate-300 px-3 py-1 text-sm"
                            value={granularity}
                            onChange={(event) => setGranularity(event.target.value)}
                        >
                            <option value="month">Theo tháng</option>
                            <option value="week">Theo tuần</option>
                        </select>
                    </div>
                </section>
            )}

            {showSeries && (
                <section className="data-table-page crm-card min-h-0 flex-1">
                    <h3 className="data-table-page__header text-base font-semibold">Chi tiết theo kỳ</h3>
                    <div className="data-table-page__body">
                        <table className="crm-table min-w-full">
                            <thead>
                                <tr>
                                    <th>Kỳ</th>
                                    <th>Đơn hàng</th>
                                    <th>Doanh thu</th>
                                    <th>Lợi nhuận</th>
                                    <th>Đã thu</th>
                                    <th>Hoàn thành</th>
                                </tr>
                            </thead>
                            <tbody>
                                {(report?.series || []).map((row) => (
                                    <tr key={row.period_key}>
                                        <td>{row.label}</td>
                                        <td>{row.orders}</td>
                                        <td>{formatJpy(row.revenue_jpy)}</td>
                                        <td>{formatJpy(row.profit_jpy)}</td>
                                        <td>{formatJpy(row.collected_jpy)}</td>
                                        <td>{row.completed_orders}</td>
                                    </tr>
                                ))}
                            </tbody>
                        </table>
                    </div>
                </section>
            )}

            {(showAffiliates || showAlerts) && (
                <section className={`grid shrink-0 gap-4 ${showAffiliates && showAlerts ? "xl:grid-cols-2" : "grid-cols-1"}`}>
                    {showAffiliates && (
                        <article className="crm-card">
                            <h3 className="mb-3 text-base font-semibold">Top cộng tác viên</h3>
                            <div className="data-table-page__body max-h-64">
                                <table className="crm-table min-w-full">
                                    <thead>
                                        <tr>
                                            <th>Mã</th>
                                            <th>Tên</th>
                                            <th>Số đơn</th>
                                            <th>Doanh số kỳ này</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {(affiliateReport?.top_affiliates || []).map((item) => (
                                            <tr key={item.id}>
                                                <td>{item.code}</td>
                                                <td>{item.full_name}</td>
                                                <td>{item.period_orders || 0}</td>
                                                <td>{formatJpy(item.period_sales_jpy || 0)}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </article>
                    )}
                    {showAlerts && (
                        <article className="crm-card">
                            <h3 className="mb-3 text-base font-semibold">Cảnh báo công nợ quá hạn</h3>
                            <ul className="space-y-2">
                                {alerts.map((alert) => (
                                    <li key={alert.id} className="rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-700">
                                        <p className="font-medium">{alert.title}</p>
                                        <p>{alert.message}</p>
                                    </li>
                                ))}
                                {alerts.length === 0 && <li className="text-sm text-slate-500">Không có cảnh báo quá hạn.</li>}
                            </ul>
                        </article>
                    )}
                </section>
            )}
        </div>
    );
}

function StatBox({ label, value, isMoney = false }) {
    return (
        <div className="rounded-lg border border-slate-200 bg-slate-50 p-3">
            <p className="text-xs uppercase tracking-wide text-slate-500">{label}</p>
            <p className="mt-1 text-xl font-semibold">
                {isMoney ? formatJpy(value) : Number(value || 0).toLocaleString("vi-VN")}
            </p>
        </div>
    );
}
