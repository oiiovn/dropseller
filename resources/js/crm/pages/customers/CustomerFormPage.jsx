import React, { useEffect, useState } from "react";
import { Link, useNavigate, useParams } from "react-router";
import { api } from "../../lib/api";
import { CONSULTATION_STATUS_LABELS } from "../../lib/orderStatuses";

const emptyForm = {
    full_name: "",
    phone: "",
    email: "",
    address: "",
    facebook_url: "",
    google_maps_url: "",
    prefecture: "",
    city: "",
    postal_code: "",
    preferred_bicycle_line: "",
    consultation_status: "new",
    purchase_interest: "",
    consultation_history: "",
};

export default function CustomerFormPage() {
    const { id } = useParams();
    const navigate = useNavigate();
    const isEdit = Boolean(id);
    const [form, setForm] = useState(emptyForm);
    const [loading, setLoading] = useState(isEdit);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState("");

    useEffect(() => {
        if (!isEdit) return;
        api.get(`/customers/${id}`)
            .then((response) => setForm({ ...emptyForm, ...response.data }))
            .catch(() => setError("Không tải được khách hàng."))
            .finally(() => setLoading(false));
    }, [id, isEdit]);

    const handleChange = (event) => {
        const { name, value } = event.target;
        setForm((current) => ({ ...current, [name]: value }));
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setSaving(true);
        setError("");
        try {
            if (isEdit) {
                await api.put(`/customers/${id}`, form);
            } else {
                await api.post("/customers", form);
            }
            navigate("/customers");
        } catch (requestError) {
            setError(requestError.response?.data?.message || "Không lưu được khách hàng.");
        } finally {
            setSaving(false);
        }
    };

    if (loading) {
        return <p className="text-sm text-slate-500">Đang tải...</p>;
    }

    return (
        <section className="crm-card max-w-3xl">
            <div className="mb-4 flex items-center justify-between">
                <h2 className="text-lg font-semibold">{isEdit ? "Cập nhật khách hàng" : "Thêm khách hàng mới"}</h2>
                <Link to="/customers" className="text-sm text-blue-600 hover:underline">
                    Quay lại
                </Link>
            </div>

            <form className="space-y-4" onSubmit={handleSubmit}>
                <div className="grid gap-4 md:grid-cols-2">
                    <label className="crm-field">
                        <span>Họ tên *</span>
                        <input name="full_name" value={form.full_name} onChange={handleChange} required />
                    </label>
                    <label className="crm-field">
                        <span>Điện thoại *</span>
                        <input name="phone" value={form.phone} onChange={handleChange} required />
                    </label>
                    <label className="crm-field">
                        <span>Email</span>
                        <input type="email" name="email" value={form.email || ""} onChange={handleChange} />
                    </label>
                    <label className="crm-field">
                        <span>Trạng thái tư vấn</span>
                        <select name="consultation_status" value={form.consultation_status || "new"} onChange={handleChange}>
                            {Object.entries(CONSULTATION_STATUS_LABELS).map(([value, label]) => (
                                <option key={value} value={value}>
                                    {label}
                                </option>
                            ))}
                        </select>
                    </label>
                    <label className="crm-field md:col-span-2">
                        <span>Địa chỉ</span>
                        <input name="address" value={form.address || ""} onChange={handleChange} />
                    </label>
                    <label className="crm-field md:col-span-2">
                        <span>Link Facebook</span>
                        <input
                            type="url"
                            name="facebook_url"
                            value={form.facebook_url || ""}
                            onChange={handleChange}
                            placeholder="https://facebook.com/..."
                        />
                    </label>
                    <label className="crm-field md:col-span-2">
                        <span>Link Google Maps</span>
                        <input
                            type="url"
                            name="google_maps_url"
                            value={form.google_maps_url || ""}
                            onChange={handleChange}
                            placeholder="https://maps.google.com/..."
                        />
                    </label>
                    {(form.facebook_url || form.google_maps_url) && (
                        <div className="md:col-span-2 flex flex-wrap gap-3 text-sm">
                            {form.facebook_url && (
                                <a href={form.facebook_url} target="_blank" rel="noreferrer" className="text-blue-600 hover:underline">
                                    Mở Facebook
                                </a>
                            )}
                            {form.google_maps_url && (
                                <a href={form.google_maps_url} target="_blank" rel="noreferrer" className="text-blue-600 hover:underline">
                                    Mở Google Maps
                                </a>
                            )}
                        </div>
                    )}
                    <label className="crm-field">
                        <span>Prefecture</span>
                        <input name="prefecture" value={form.prefecture || ""} onChange={handleChange} />
                    </label>
                    <label className="crm-field">
                        <span>Thành phố</span>
                        <input name="city" value={form.city || ""} onChange={handleChange} />
                    </label>
                    <label className="crm-field">
                        <span>Mã bưu điện</span>
                        <input name="postal_code" value={form.postal_code || ""} onChange={handleChange} />
                    </label>
                    <label className="crm-field">
                        <span>Dòng xe quan tâm</span>
                        <input name="preferred_bicycle_line" value={form.preferred_bicycle_line || ""} onChange={handleChange} />
                    </label>
                    <label className="crm-field md:col-span-2">
                        <span>Nhu cầu mua</span>
                        <input name="purchase_interest" value={form.purchase_interest || ""} onChange={handleChange} />
                    </label>
                    <label className="crm-field md:col-span-2">
                        <span>Lịch sử tư vấn</span>
                        <textarea name="consultation_history" rows={4} value={form.consultation_history || ""} onChange={handleChange} />
                    </label>
                </div>

                {error && <p className="text-sm text-red-600">{error}</p>}

                <div className="flex gap-2">
                    <button type="submit" className="crm-btn-primary" disabled={saving}>
                        {saving ? "Đang lưu..." : isEdit ? "Cập nhật" : "Tạo khách hàng"}
                    </button>
                    <Link to="/customers" className="crm-btn-secondary">
                        Hủy
                    </Link>
                </div>
            </form>
        </section>
    );
}
