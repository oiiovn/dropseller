import React, { useEffect, useState } from "react";

export default function RejectPaymentModal({ open, saving, error, onClose, onSubmit }) {
    const [note, setNote] = useState("");
    const [localError, setLocalError] = useState("");

    useEffect(() => {
        if (open) {
            setNote("");
            setLocalError("");
        }
    }, [open]);

    if (!open) {
        return null;
    }

    const handleSubmit = (event) => {
        event.preventDefault();
        const trimmed = note.trim();
        if (!trimmed) {
            setLocalError("Vui lòng nhập lý do từ chối.");
            return;
        }
        onSubmit(trimmed);
    };

    return (
        <div className="reject-payment-modal">
            <button type="button" className="reject-payment-modal__backdrop" onClick={onClose} aria-label="Đóng" />
            <section className="reject-payment-modal__panel" role="dialog" aria-modal="true" aria-labelledby="reject-payment-title">
                <div className="reject-payment-modal__header">
                    <h3 id="reject-payment-title">Từ chối thanh toán</h3>
                    <button type="button" className="reject-payment-modal__close" onClick={onClose}>
                        Đóng
                    </button>
                </div>

                <form className="reject-payment-modal__form" onSubmit={handleSubmit}>
                    <label className="reject-payment-modal__field">
                        <span>Lý do từ chối *</span>
                        <textarea
                            rows={4}
                            value={note}
                            onChange={(event) => {
                                setNote(event.target.value);
                                if (localError) setLocalError("");
                            }}
                            placeholder="Nhập lý do kế toán từ chối thanh toán này..."
                            autoFocus
                            required
                        />
                    </label>

                    {(localError || error) && <p className="reject-payment-modal__error">{localError || error}</p>}

                    <div className="reject-payment-modal__actions">
                        <button type="button" className="crm-btn-secondary" onClick={onClose} disabled={saving}>
                            Hủy
                        </button>
                        <button type="submit" className="crm-btn-danger" disabled={saving}>
                            {saving ? "Đang lưu..." : "Xác nhận từ chối"}
                        </button>
                    </div>
                </form>
            </section>
        </div>
    );
}
