import React, { useEffect, useState } from "react";
import { adminApi } from "../lib/api";
import TablePagination from "../../shared/components/TablePagination";

const DEFAULT_PER_PAGE = 15;

export default function EntityListPage({ title, endpoint, columns, action, perPage = DEFAULT_PER_PAGE }) {
    const [rows, setRows] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");
    const [meta, setMeta] = useState({ current_page: 1, last_page: 1, total: 0 });

    const load = (page = 1) => {
        setLoading(true);
        setError("");

        adminApi
            .get(endpoint, { params: { page, per_page: perPage } })
            .then((response) => {
                setRows(response.data?.data ?? response.data ?? []);
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
        load(1);
    }, [endpoint, perPage]);

    return (
        <section className="data-table-page admin-card">
            <div className="data-table-page__header flex items-center justify-between gap-3">
                <h2 className="text-lg font-semibold">{title}</h2>
                {action}
            </div>

            <div className="data-table-page__messages">
                {loading && <p className="text-sm text-slate-500">Đang tải...</p>}
                {error && <p className="text-sm text-red-600">{error}</p>}
            </div>

            {!error && (
                <>
                    <div className="data-table-page__body">
                        <table className="admin-table min-w-full">
                            <thead>
                                <tr>
                                    {columns.map((column) => (
                                        <th key={column.key}>{column.label}</th>
                                    ))}
                                </tr>
                            </thead>
                            <tbody>
                                {!loading &&
                                    rows.map((row) => (
                                        <tr key={row.id}>
                                            {columns.map((column) => (
                                                <td key={column.key}>{column.render ? column.render(row) : row[column.key]}</td>
                                            ))}
                                        </tr>
                                    ))}
                                {!loading && rows.length === 0 && (
                                    <tr>
                                        <td colSpan={columns.length} className="py-6 text-center text-sm text-slate-500">
                                            Chưa có dữ liệu
                                        </td>
                                    </tr>
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
                            buttonClassName="admin-btn-secondary px-3 py-1.5 text-xs"
                        />
                    </div>
                </>
            )}
        </section>
    );
}
