import React, { useEffect, useState } from "react";
import { DEFAULT_THEME, useTheme } from "../../shared/context/ThemeContext";

const COLOR_FIELDS = [
    {
        key: "background_color",
        label: "Màu nền trang",
        hint: "Vùng nội dung chính phía sau các thẻ dữ liệu",
    },
    {
        key: "sidebar_background_color",
        label: "Màu nền sidebar",
        hint: "Thanh menu bên trái",
    },
    {
        key: "sidebar_text_color",
        label: "Màu chữ sidebar",
        hint: "Màu chữ mục menu chưa chọn",
    },
    {
        key: "sidebar_active_color",
        label: "Màu mục đang chọn",
        hint: "Nền của mục menu đang active",
    },
    {
        key: "header_background_color",
        label: "Màu nền header",
        hint: "Thanh tiêu đề phía trên",
    },
];

function ColorField({ field, value, onChange }) {
    return (
        <label className="admin-field">
            <span>{field.label}</span>
            <div className="flex items-center gap-3">
                <input
                    type="color"
                    name={field.key}
                    value={value}
                    onChange={onChange}
                    className="h-11 w-14 cursor-pointer rounded-lg border border-slate-200 bg-white p-1"
                />
                <input
                    type="text"
                    name={field.key}
                    value={value}
                    onChange={onChange}
                    pattern="^#[0-9A-Fa-f]{6}$"
                    className="max-w-[8rem] font-mono uppercase"
                    placeholder="#000000"
                />
            </div>
            <p className="mt-1 text-xs text-slate-500">{field.hint}</p>
        </label>
    );
}

export default function AdminAppearancePage() {
    const { theme, updateTheme } = useTheme();
    const [form, setForm] = useState(DEFAULT_THEME);
    const [saving, setSaving] = useState(false);
    const [message, setMessage] = useState("");
    const [error, setError] = useState("");

    useEffect(() => {
        setForm(theme);
    }, [theme]);

    const handleChange = (event) => {
        const { name, value } = event.target;
        setForm((current) => ({ ...current, [name]: value }));
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setSaving(true);
        setMessage("");
        setError("");

        try {
            await updateTheme(form);
            setMessage("Đã lưu cấu hình giao diện.");
        } catch (requestError) {
            setError(requestError.response?.data?.message || "Không lưu được cấu hình.");
        } finally {
            setSaving(false);
        }
    };

    const handleReset = () => {
        setForm(DEFAULT_THEME);
        setMessage("");
        setError("");
    };

    return (
        <div className="mx-auto max-w-3xl space-y-6">
            <div className="admin-card">
                <h2 className="text-lg font-semibold text-slate-900">Cấu hình giao diện</h2>
                <p className="mt-1 text-sm text-slate-500">
                    Tùy chỉnh màu nền trang, sidebar và header. Thay đổi áp dụng cho toàn bộ hệ thống (Admin và CRM).
                </p>
            </div>

            <form onSubmit={handleSubmit} className="admin-card space-y-6">
                <div className="grid gap-5 md:grid-cols-2">
                    {COLOR_FIELDS.map((field) => (
                        <ColorField
                            key={field.key}
                            field={field}
                            value={form[field.key]}
                            onChange={handleChange}
                        />
                    ))}
                </div>

                <div className="rounded-xl border border-slate-200 p-4">
                    <p className="mb-3 text-sm font-medium text-slate-700">Xem trước</p>
                    <div className="overflow-hidden rounded-lg border border-slate-200">
                        <div
                            className="flex h-28"
                            style={{ backgroundColor: form.background_color }}
                        >
                            <div
                                className="w-24 shrink-0 border-r p-2"
                                style={{
                                    backgroundColor: form.sidebar_background_color,
                                    borderColor: "rgba(255,255,255,0.1)",
                                    color: form.sidebar_text_color,
                                }}
                            >
                                <div className="mb-2 text-[10px] font-bold">Menu</div>
                                <div
                                    className="rounded px-1.5 py-1 text-[10px] text-white"
                                    style={{ backgroundColor: form.sidebar_active_color }}
                                >
                                    Active
                                </div>
                                <div className="mt-1 px-1.5 py-1 text-[10px]">Item</div>
                            </div>
                            <div className="flex min-w-0 flex-1 flex-col">
                                <div
                                    className="border-b px-3 py-2 text-xs font-semibold text-slate-700"
                                    style={{
                                        backgroundColor: form.header_background_color,
                                        borderColor: "rgba(0,0,0,0.08)",
                                    }}
                                >
                                    Header
                                </div>
                                <div className="flex-1 p-3">
                                    <div className="h-full rounded-md bg-white/80 shadow-sm" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {message && (
                    <div className="rounded-lg border border-emerald-200 bg-emerald-50 px-3 py-2 text-sm text-emerald-700">
                        {message}
                    </div>
                )}
                {error && (
                    <div className="rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                        {error}
                    </div>
                )}

                <div className="flex flex-wrap gap-2">
                    <button type="submit" className="admin-btn-primary" disabled={saving}>
                        {saving ? "Đang lưu..." : "Lưu cấu hình"}
                    </button>
                    <button type="button" className="admin-btn-secondary" onClick={handleReset}>
                        Khôi phục mặc định
                    </button>
                </div>
            </form>
        </div>
    );
}
