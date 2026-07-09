import React, { useEffect, useState } from "react";
import { adminApi } from "../lib/api";
import TablePagination from "../../shared/components/TablePagination";

const emptyForm = {
    name: "",
    bike_line: "",
    description: "",
    is_active: true,
    sort_order: "0",
};

function PinFormModal({ pin, onClose, onSaved }) {
    const isEdit = Boolean(pin?.id);
    const [form, setForm] = useState(emptyForm);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState("");

    useEffect(() => {
        if (pin) {
            setForm({
                name: pin.name || "",
                bike_line: pin.bike_line || "",
                description: pin.description || "",
                is_active: Boolean(pin.is_active),
                sort_order: String(pin.sort_order ?? 0),
            });
        } else {
            setForm(emptyForm);
        }
    }, [pin]);

    const handleChange = (event) => {
        const { name, value, type, checked } = event.target;
        setForm((current) => ({
            ...current,
            [name]: type === "checkbox" && name === "is_active" ? checked : value,
        }));
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setSaving(true);
        setError("");

        const payload = {
            name: form.name.trim(),
            bike_line: form.bike_line.trim() || null,
            description: form.description.trim() || null,
            is_active: form.is_active,
            sort_order: Number(form.sort_order || 0),
        };

        try {
            if (isEdit) {
                await adminApi.put(`/pins/${pin.id}`, payload);
            } else {
                await adminApi.post("/pins", payload);
            }
            onSaved();
        } catch (requestError) {
            setError(requestError.response?.data?.message || "Không lưu được pin.");
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
            <section className="admin-card max-h-[90vh] w-full max-w-lg overflow-y-auto">
                <div className="mb-4 flex items-center justify-between">
                    <h3 className="text-lg font-semibold">{isEdit ? "Sửa pin" : "Thêm pin"}</h3>
                    <button type="button" className="text-sm text-slate-500 hover:text-slate-700" onClick={onClose}>
                        Đóng
                    </button>
                </div>

                <form className="space-y-4" onSubmit={handleSubmit}>
                    <label className="admin-field">
                        <span>Tên pin *</span>
                        <input
                            name="name"
                            value={form.name}
                            onChange={handleChange}
                            required
                            placeholder="VD: 8.7Ah Panasonic"
                        />
                    </label>

                    <label className="admin-field">
                        <span>Dòng xe</span>
                        <input
                            name="bike_line"
                            value={form.bike_line}
                            onChange={handleChange}
                            placeholder="VD: Yamaha PAS / Panasonic Gyutto"
                        />
                    </label>

                    <label className="admin-field">
                        <span>Mô tả pin</span>
                        <textarea
                            name="description"
                            value={form.description}
                            onChange={handleChange}
                            rows={3}
                            placeholder="Thông tin bổ sung cho CTV khi chọn pin..."
                        />
                    </label>

                    <label className="admin-field">
                        <span>Thứ tự hiển thị</span>
                        <input
                            type="number"
                            min="0"
                            name="sort_order"
                            value={form.sort_order}
                            onChange={handleChange}
                        />
                    </label>

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
                            {saving ? "Đang lưu..." : isEdit ? "Lưu thay đổi" : "Thêm pin"}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    );
}

export default function PinListPage() {
    const [rows, setRows] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState("");
    const [actionError, setActionError] = useState("");
    const [editingPin, setEditingPin] = useState(null);
    const [creating, setCreating] = useState(false);

    const [meta, setMeta] = useState({ current_page: 1, last_page: 1, total: 0 });

    const load = (page = 1) => {
        setLoading(true);
        adminApi
            .get("/pins", { params: { page, per_page: 15 } })
            .then((response) => {
                setRows(response.data?.data ?? response.data ?? []);
                setMeta({
                    current_page: response.data?.current_page ?? 1,
                    last_page: response.data?.last_page ?? 1,
                    total: response.data?.total ?? 0,
                });
            })
            .catch(() => setError("Không tải được danh sách pin."))
            .finally(() => setLoading(false));
    };

    useEffect(() => {
        load();
    }, []);

    const openEdit = async (row) => {
        try {
            const { data } = await adminApi.get(`/pins/${row.id}`);
            setEditingPin(data);
        } catch {
            setActionError("Không tải được chi tiết pin.");
        }
    };

    const handleDelete = async (pin) => {
        if (!window.confirm(`Xóa pin "${pin.name}"?`)) {
            return;
        }

        setActionError("");
        try {
            await adminApi.delete(`/pins/${pin.id}`);
            load();
        } catch (requestError) {
            setActionError(requestError.response?.data?.message || "Không xóa được pin.");
        }
    };

    return (
        <>
            <section className="data-table-page admin-card">
                <div className="data-table-page__header flex items-center justify-between gap-3">
                    <div>
                        <h2 className="text-lg font-semibold">Quản lý pin</h2>
                        <p className="text-sm text-slate-500">Danh sách pin dùng chung cho toàn bộ CTV khi tạo đơn hàng</p>
                    </div>
                    <button type="button" className="admin-btn-primary" onClick={() => setCreating(true)}>
                        + Thêm pin
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
                                <th>Tên pin</th>
                                <th>Dòng xe</th>
                                <th>Mô tả</th>
                                <th>Thứ tự</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            {rows.length === 0 && (
                                <tr>
                                    <td colSpan={6} className="py-6 text-center text-slate-500">
                                        Chưa có pin nào.
                                    </td>
                                </tr>
                            )}
                            {rows.map((row) => (
                                <tr key={row.id}>
                                    <td className="font-medium">{row.name}</td>
                                    <td>{row.bike_line || "—"}</td>
                                    <td className="max-w-md text-slate-600">{row.description || "—"}</td>
                                    <td>{row.sort_order ?? 0}</td>
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
                <PinFormModal
                    onClose={() => setCreating(false)}
                    onSaved={() => {
                        setCreating(false);
                        load();
                    }}
                />
            )}

            {editingPin && (
                <PinFormModal
                    pin={editingPin}
                    onClose={() => setEditingPin(null)}
                    onSaved={() => {
                        setEditingPin(null);
                        load();
                    }}
                />
            )}
        </>
    );
}
