import React, { useState } from "react";
import { adminApi } from "../lib/api";
import { AFFILIATE_REGIONS } from "../lib/affiliateRegions";

export default function AssignCollaboratorModal({ user, onClose, onSaved }) {
    const [form, setForm] = useState({
        code: "",
        full_name: user.name || "",
        phone: user.phone || "",
        area: "",
        region: "japan",
    });
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState("");
    const phoneRequired = !user.phone;

    const handleChange = (event) => {
        const { name, value } = event.target;
        setForm((current) => ({ ...current, [name]: value }));
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setSaving(true);
        setError("");
        try {
            await adminApi.post("/collaborators", {
                user_id: user.id,
                code: form.code,
                full_name: form.full_name || undefined,
                phone: form.phone || undefined,
                area: form.area || undefined,
                region: form.region,
            });
            onSaved();
        } catch (requestError) {
            setError(requestError.response?.data?.message || "Không gán được CTV.");
        } finally {
            setSaving(false);
        }
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 p-4">
            <section className="admin-card w-full max-w-lg">
                <div className="mb-4 flex items-center justify-between">
                    <h3 className="text-lg font-semibold">Gán CTV</h3>
                    <button type="button" className="text-sm text-slate-500 hover:text-slate-700" onClick={onClose}>
                        Đóng
                    </button>
                </div>

                <p className="mb-4 text-sm text-slate-600">
                    Tài khoản: <span className="font-medium">{user.name}</span> ({user.email})
                </p>

                <form className="space-y-4" onSubmit={handleSubmit}>
                    <div className="grid gap-4 md:grid-cols-2">
                        <label className="admin-field">
                            <span>Mã CTV *</span>
                            <input name="code" value={form.code} onChange={handleChange} required />
                        </label>
                        <label className="admin-field">
                            <span>Họ tên</span>
                            <input
                                name="full_name"
                                value={form.full_name}
                                onChange={handleChange}
                                placeholder="Lấy từ tài khoản nếu để trống"
                            />
                        </label>
                        <label className="admin-field">
                            <span>Điện thoại{phoneRequired ? " *" : ""}</span>
                            <input name="phone" value={form.phone} onChange={handleChange} required={phoneRequired} />
                        </label>
                        <label className="admin-field">
                            <span>Khu vực *</span>
                            <select name="region" value={form.region} onChange={handleChange} required>
                                {AFFILIATE_REGIONS.map((region) => (
                                    <option key={region.value} value={region.value}>
                                        {region.label}
                                    </option>
                                ))}
                            </select>
                        </label>
                        <label className="admin-field md:col-span-2">
                            <span>Ghi chú khu vực (tuỳ chọn)</span>
                            <input
                                name="area"
                                value={form.area}
                                onChange={handleChange}
                                placeholder="VD: Tokyo, Osaka..."
                            />
                        </label>
                    </div>

                    {error && <p className="text-sm text-red-600">{error}</p>}

                    <div className="flex justify-end gap-2">
                        <button type="button" className="admin-btn-secondary" onClick={onClose}>
                            Hủy
                        </button>
                        <button type="submit" className="admin-btn-primary" disabled={saving}>
                            {saving ? "Đang gán..." : "Gán CTV"}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    );
}
