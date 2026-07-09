import React, { useEffect, useState } from "react";
import { adminApi } from "../lib/api";
import TablePagination from "../../shared/components/TablePagination";

const emptyForm = {
    name: "",
    product_category: "",
    rate_percent: "",
    is_active: true,
    affiliate_ids: [],
};

function RuleFormModal({ rule, onClose, onSaved }) {
    const isEdit = Boolean(rule?.id);
    const [form, setForm] = useState(emptyForm);
    const [affiliates, setAffiliates] = useState([]);
    const [loadingAffiliates, setLoadingAffiliates] = useState(true);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState("");

    useEffect(() => {
        adminApi
            .get("/affiliates", { params: { per_page: 200, status: "active" } })
            .then((response) => setAffiliates(response.data?.data ?? response.data ?? []))
            .finally(() => setLoadingAffiliates(false));
    }, []);

    useEffect(() => {
        if (rule) {
            setForm({
                name: rule.name || "",
                product_category: rule.product_category || "",
                rate_percent: String(rule.rate_percent ?? ""),
                is_active: Boolean(rule.is_active),
                affiliate_ids: (rule.affiliates || []).map((item) => item.id),
            });
        } else {
            setForm(emptyForm);
        }
    }, [rule]);

    const handleChange = (event) => {
        const { name, value, type, checked } = event.target;
        setForm((current) => ({
            ...current,
            [name]: type === "checkbox" && name === "is_active" ? checked : value,
        }));
    };

    const toggleAffiliate = (affiliateId) => {
        setForm((current) => ({
            ...current,
            affiliate_ids: current.affiliate_ids.includes(affiliateId)
                ? current.affiliate_ids.filter((id) => id !== affiliateId)
                : [...current.affiliate_ids, affiliateId],
        }));
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setSaving(true);
        setError("");

        const payload = {
            name: form.name.trim(),
            product_category: form.product_category.trim() || null,
            rate_percent: Number(form.rate_percent),
            is_active: form.is_active,
            affiliate_ids: form.affiliate_ids,
        };

        try {
            if (isEdit) {
                await adminApi.put(`/commission-rules/${rule.id}`, payload);
            } else {
                await adminApi.post("/commission-rules", payload);
            }
            onSaved();
        } catch (requestError) {
            setError(requestError.response?.data?.message || "Không lưu được rule.");
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
            <section className="admin-card max-h-[90vh] w-full max-w-lg overflow-y-auto">
                <div className="mb-4 flex items-center justify-between">
                    <h3 className="text-lg font-semibold">{isEdit ? "Sửa rule hoa hồng" : "Thêm rule hoa hồng"}</h3>
                    <button type="button" className="text-sm text-slate-500 hover:text-slate-700" onClick={onClose}>
                        Đóng
                    </button>
                </div>

                <form className="space-y-4" onSubmit={handleSubmit}>
                    <label className="admin-field">
                        <span>Tên rule *</span>
                        <input name="name" value={form.name} onChange={handleChange} required placeholder="VD: Mặc định" />
                    </label>

                    <label className="admin-field">
                        <span>Dòng xe</span>
                        <input
                            name="product_category"
                            value={form.product_category}
                            onChange={handleChange}
                            placeholder="VD: sports — để trống = mọi dòng"
                        />
                    </label>

                    <label className="admin-field">
                        <span>Hoa hồng (%) *</span>
                        <input
                            type="number"
                            name="rate_percent"
                            value={form.rate_percent}
                            onChange={handleChange}
                            min="0"
                            max="100"
                            step="0.01"
                            required
                        />
                    </label>

                    <div>
                        <p className="mb-2 text-sm font-medium text-slate-700">CTV áp dụng rule</p>
                        <p className="mb-2 text-xs text-slate-500">
                            CTV được gắn vào rule sẽ dùng tỉ lệ của rule đó khi đơn hoàn thành. Mỗi CTV chỉ thuộc một rule tại một thời điểm.
                            Không chọn CTV = rule mặc định cho CTV chưa được gắn rule riêng.
                        </p>
                        {loadingAffiliates ? (
                            <p className="text-sm text-slate-500">Đang tải danh sách CTV...</p>
                        ) : affiliates.length === 0 ? (
                            <p className="text-sm text-amber-700">Chưa có CTV trong hệ thống.</p>
                        ) : (
                            <div className="max-h-48 space-y-1 overflow-y-auto rounded-lg border border-slate-200 p-2">
                                {affiliates.map((affiliate) => (
                                    <label
                                        key={affiliate.id}
                                        className="flex cursor-pointer items-center gap-2 rounded px-2 py-1.5 text-sm hover:bg-slate-50"
                                    >
                                        <input
                                            type="checkbox"
                                            checked={form.affiliate_ids.includes(affiliate.id)}
                                            onChange={() => toggleAffiliate(affiliate.id)}
                                        />
                                        <span className="font-medium">{affiliate.code}</span>
                                        <span className="text-slate-500">{affiliate.full_name}</span>
                                    </label>
                                ))}
                            </div>
                        )}
                        {form.affiliate_ids.length > 0 && (
                            <p className="mt-1 text-xs text-blue-600">Đã chọn {form.affiliate_ids.length} CTV</p>
                        )}
                    </div>

                    <label className="inline-flex cursor-pointer items-center gap-2 text-sm">
                        <input type="checkbox" name="is_active" checked={form.is_active} onChange={handleChange} />
                        <span>Đang dùng</span>
                    </label>

                    {error && <p className="text-sm text-red-600">{error}</p>}

                    <div className="flex justify-end gap-2">
                        <button type="button" className="admin-btn-secondary" onClick={onClose}>
                            Hủy
                        </button>
                        <button type="submit" className="admin-btn-primary" disabled={saving}>
                            {saving ? "Đang lưu..." : isEdit ? "Lưu thay đổi" : "Thêm rule"}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    );
}

export default function CommissionRulesPage() {
    const [rows, setRows] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");
    const [actionError, setActionError] = useState("");
    const [editingRule, setEditingRule] = useState(null);
    const [creating, setCreating] = useState(false);

    const [meta, setMeta] = useState({ current_page: 1, last_page: 1, total: 0 });

    const load = (page = 1) => {
        setLoading(true);
        adminApi
            .get("/commission-rules", { params: { page, per_page: 15 } })
            .then((response) => {
                setRows(response.data?.data ?? response.data ?? []);
                setMeta({
                    current_page: response.data?.current_page ?? 1,
                    last_page: response.data?.last_page ?? 1,
                    total: response.data?.total ?? 0,
                });
            })
            .catch(() => setError("Không tải được danh sách rule."))
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        load();
    }, []);

    const openEdit = async (row) => {
        try {
            const { data } = await adminApi.get(`/commission-rules/${row.id}`);
            setEditingRule(data);
        } catch {
            setActionError("Không tải được chi tiết rule.");
        }
    };

    const handleDelete = async (rule) => {
        if (!window.confirm(`Xóa rule "${rule.name}"?`)) {
            return;
        }

        setActionError("");
        try {
            await adminApi.delete(`/commission-rules/${rule.id}`);
            load();
        } catch (requestError) {
            setActionError(requestError.response?.data?.message || "Không xóa được rule.");
        }
    };

    return (
        <>
            <section className="data-table-page admin-card">
                <div className="data-table-page__header flex items-center justify-between gap-3">
                    <div>
                        <h2 className="text-lg font-semibold">Rule hoa hồng linh hoạt</h2>
                        <p className="text-sm text-slate-500">Gán CTV cụ thể hoặc để trống = áp dụng mọi CTV</p>
                    </div>
                    <button type="button" className="admin-btn-primary" onClick={() => setCreating(true)}>
                        + Thêm rule
                    </button>
                </div>

                {error && <p className="mb-3 text-sm text-red-600">{error}</p>}
                {actionError && <p className="mb-3 text-sm text-red-600">{actionError}</p>}
                {loading && <p className="text-sm text-slate-500">Đang tải...</p>}

                {!loading && !error && (
                    <>
                        <div className="data-table-page__body">
                            <table className="admin-table min-w-full">
                        <thead>
                            <tr>
                                <th>Tên rule</th>
                                <th>Dòng xe</th>
                                <th>Hoa hồng (%)</th>
                                <th>Số CTV</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            {rows.length === 0 && (
                                <tr>
                                    <td colSpan={6} className="py-6 text-center text-slate-500">
                                        Chưa có rule nào.
                                    </td>
                                </tr>
                            )}
                            {rows.map((row) => (
                                <tr key={row.id}>
                                    <td className="font-medium">{row.name}</td>
                                    <td>{row.product_category || "—"}</td>
                                    <td>{Number(row.rate_percent).toFixed(2)}</td>
                                    <td>{row.affiliates_count ?? 0}</td>
                                    <td>
                                        <span
                                            className={`admin-badge ${
                                                row.is_active ? "bg-emerald-50 text-emerald-700" : "bg-slate-100 text-slate-600"
                                            }`}
                                        >
                                            {row.is_active ? "Đang dùng" : "Tạm tắt"}
                                        </span>
                                    </td>
                                    <td className="space-x-2 whitespace-nowrap">
                                        <button
                                            type="button"
                                            className="text-sm text-blue-600 hover:underline"
                                            onClick={() => openEdit(row)}
                                        >
                                            Sửa
                                        </button>
                                        <button
                                            type="button"
                                            className="text-sm text-red-600 hover:underline"
                                            onClick={() => handleDelete(row)}
                                        >
                                            Xóa
                                        </button>
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

            {creating && (
                <RuleFormModal
                    onClose={() => setCreating(false)}
                    onSaved={() => {
                        setCreating(false);
                        load();
                    }}
                />
            )}

            {editingRule && (
                <RuleFormModal
                    rule={editingRule}
                    onClose={() => setEditingRule(null)}
                    onSaved={() => {
                        setEditingRule(null);
                        load();
                    }}
                />
            )}
        </>
    );
}
