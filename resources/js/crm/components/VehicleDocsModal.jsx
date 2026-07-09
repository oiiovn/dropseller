import React, { useEffect, useState } from "react";
import JapaneseNameInput from "./JapaneseNameInput";
import { isValidJapaneseName, JAPANESE_NAME_HINT } from "../lib/japaneseInput";

const EMPTY_FORM = {
    chassis_number: "",
    owner_name_vi: "",
    owner_name_ja: "",
    postal_code: "",
    phone: "",
    address: "",
};

const inputClass =
    "w-full rounded-lg border border-slate-200 px-3 py-2 text-sm focus:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-100";

export default function VehicleDocsModal({ open, saving, error, initialValues, onClose, onSubmit }) {
    const [form, setForm] = useState(EMPTY_FORM);
    const [localError, setLocalError] = useState("");

    useEffect(() => {
        if (open) {
            setForm({
                chassis_number: initialValues?.chassis_number || "",
                owner_name_vi: initialValues?.owner_name_vi || "",
                owner_name_ja: initialValues?.owner_name_ja || "",
                postal_code: initialValues?.postal_code || "",
                phone: initialValues?.phone || "",
                address: initialValues?.address || "",
            });
            setLocalError("");
        }
    }, [open, initialValues]);

    if (!open) {
        return null;
    }

    const handleSubmit = (event) => {
        event.preventDefault();
        if (!isValidJapaneseName(form.owner_name_ja)) {
            setLocalError(JAPANESE_NAME_HINT);
            return;
        }
        onSubmit(form);
    };

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4">
            <button type="button" className="absolute inset-0 bg-slate-900/40" onClick={onClose} aria-label="Đóng" />
            <section
                className="relative z-10 max-h-[90vh] w-full max-w-lg overflow-hidden rounded-xl border border-slate-200 bg-white p-4 shadow-xl"
                role="dialog"
                aria-modal="true"
                aria-labelledby="vehicle-docs-title"
            >
                <div className="mb-4 flex items-center justify-between gap-3">
                    <h3 id="vehicle-docs-title" className="text-base font-semibold text-slate-900">
                        Cập nhật giấy tờ xe
                    </h3>
                    <button type="button" className="text-sm text-slate-500 transition hover:text-slate-700" onClick={onClose}>
                        Đóng
                    </button>
                </div>

                <form className="max-h-[70vh] space-y-3 overflow-y-auto pr-1" onSubmit={handleSubmit}>
                    <label className="block text-sm">
                        <span className="mb-1 block font-medium text-slate-700">Số khung xe</span>
                        <input
                            className={inputClass}
                            value={form.chassis_number}
                            onChange={(event) => setForm((current) => ({ ...current, chassis_number: event.target.value }))}
                            placeholder="Nhập số khung"
                        />
                    </label>
                    <label className="block text-sm">
                        <span className="mb-1 block font-medium text-slate-700">Họ tên tiếng Việt</span>
                        <input
                            className={inputClass}
                            value={form.owner_name_vi}
                            onChange={(event) => setForm((current) => ({ ...current, owner_name_vi: event.target.value }))}
                            placeholder="Nguyễn Văn A"
                        />
                    </label>
                    <label className="block text-sm">
                        <span className="mb-1 block font-medium text-slate-700">Họ tên tiếng Nhật</span>
                        <JapaneseNameInput
                            className={inputClass}
                            value={form.owner_name_ja}
                            onChange={(value) => setForm((current) => ({ ...current, owner_name_ja: value }))}
                            placeholder="ヤマダ タロウ"
                        />
                    </label>
                    <div className="grid gap-3 sm:grid-cols-2">
                        <label className="block text-sm">
                            <span className="mb-1 block font-medium text-slate-700">Mã bưu điện</span>
                            <input
                                className={inputClass}
                                value={form.postal_code}
                                onChange={(event) => setForm((current) => ({ ...current, postal_code: event.target.value }))}
                                placeholder="113-0032"
                            />
                        </label>
                        <label className="block text-sm">
                            <span className="mb-1 block font-medium text-slate-700">SĐT</span>
                            <input
                                className={inputClass}
                                value={form.phone}
                                onChange={(event) => setForm((current) => ({ ...current, phone: event.target.value }))}
                                placeholder="09012345678"
                            />
                        </label>
                    </div>
                    <label className="block text-sm">
                        <span className="mb-1 block font-medium text-slate-700">Địa chỉ</span>
                        <textarea
                            className={`${inputClass} min-h-[72px] resize-y`}
                            rows={3}
                            value={form.address}
                            onChange={(event) => setForm((current) => ({ ...current, address: event.target.value }))}
                            placeholder="Địa chỉ trên giấy tờ xe"
                        />
                    </label>

                    {(localError || error) && <p className="text-sm text-red-600">{localError || error}</p>}

                    <div className="flex justify-end gap-2 pt-1">
                        <button
                            type="button"
                            className="rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50 disabled:opacity-50"
                            onClick={onClose}
                            disabled={saving}
                        >
                            Hủy
                        </button>
                        <button
                            type="submit"
                            className="rounded-lg bg-gradient-to-r from-blue-600 to-blue-600 px-3 py-2 text-sm font-semibold text-white hover:from-blue-700 hover:to-blue-700 disabled:opacity-60"
                            disabled={saving}
                        >
                            {saving ? "Đang lưu..." : "Lưu giấy tờ"}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    );
}
