import React, { useEffect, useMemo, useState } from "react";
import { Link, useNavigate } from "react-router";
import { api, formatJpy } from "../../lib/api";
import {
    ORDER_STATUS_LABELS,
    orderStatusBadgeClass,
    paymentStatusBadgeClass,
    paymentStatusLabel,
    shippingModeBadgeClass,
    shippingModeLabel,
} from "../../lib/orderStatuses";

const PAYMENT_METHODS = [
    "Nhận hàng kiểm tra rồi thanh toán",
    "Chuyển khoản trước",
    "Thanh toán một phần",
];

const SHIPPING_MODES = [
    { value: "boxed", label: "Đóng thùng" },
    { value: "direct_ship", label: "Ship trực tiếp" },
];

const emptyForm = {
    customer_id: "",
    product_name: "",
    pin_id: "",
    bike_price_jpy: "",
    shipping_fee_jpy: "",
    payment_method: PAYMENT_METHODS[0],
    shipping_mode: "boxed",
    delivery_date_from: "",
    delivery_date_to: "",
    delivery_time_slot: "",
    notes: "",
};

function formatPreviewDate(value) {
    if (!value) return null;
    return String(value).slice(0, 10);
}

function formatDeliveryPreview(from, to) {
    const fromText = formatPreviewDate(from);
    const toText = formatPreviewDate(to);
    if (fromText && toText && fromText !== toText) {
        return `${fromText} → ${toText}`;
    }
    return fromText || toText;
}

function PreviewRow({ label, children, empty = false }) {
    return (
        <div className={`order-form-preview__row${empty ? " is-empty" : ""}`}>
            <span className="order-form-preview__label">{label}</span>
            <span className="order-form-preview__value">{children}</span>
        </div>
    );
}

function OrderPreview({ form, customer, selectedPin, totalAmount }) {
    const deliveryRange = formatDeliveryPreview(form.delivery_date_from, form.delivery_date_to);
    const customerAddress = [customer?.address, customer?.city, customer?.prefecture, customer?.postal_code]
        .filter(Boolean)
        .join(", ");

    return (
        <aside className="order-form-preview crm-card">
            <div className="order-form-preview__header">
                <p className="order-form-preview__eyebrow">Bản xem trước</p>
                <h3 className="order-form-preview__title">Đơn hàng mới</h3>
                <div className="order-form-preview__badges">
                    <span className={orderStatusBadgeClass("new")}>{ORDER_STATUS_LABELS.new}</span>
                    <span className={paymentStatusBadgeClass("unpaid")}>{paymentStatusLabel("unpaid")}</span>
                </div>
            </div>

            <div className="order-form-preview__section">
                <h4 className="order-form-preview__section-title">Khách hàng</h4>
                <PreviewRow label="Tên" empty={!customer?.full_name}>
                    {customer?.full_name || "—"}
                </PreviewRow>
                <PreviewRow label="SĐT" empty={!customer?.phone}>
                    {customer?.phone || "—"}
                </PreviewRow>
                <PreviewRow label="Địa chỉ" empty={!customerAddress}>
                    {customerAddress || "—"}
                </PreviewRow>
            </div>

            <div className="order-form-preview__section">
                <h4 className="order-form-preview__section-title">Sản phẩm</h4>
                <PreviewRow label="Xe" empty={!form.product_name.trim()}>
                    {form.product_name.trim() || "—"}
                </PreviewRow>
                <PreviewRow label="Pin" empty={!selectedPin}>
                    {selectedPin ? (selectedPin.bike_line ? `${selectedPin.bike_line} — ${selectedPin.name}` : selectedPin.name) : "—"}
                </PreviewRow>
            </div>

            <div className="order-form-preview__section">
                <h4 className="order-form-preview__section-title">Thanh toán</h4>
                <PreviewRow label="Giá xe" empty={!form.bike_price_jpy}>
                    {form.bike_price_jpy ? formatJpy(form.bike_price_jpy) : "—"}
                </PreviewRow>
                <PreviewRow label="Phí ship" empty={!form.shipping_fee_jpy}>
                    {form.shipping_fee_jpy ? formatJpy(form.shipping_fee_jpy) : "0円"}
                </PreviewRow>
                <div className="order-form-preview__total">
                    <span>Tổng thanh toán</span>
                    <strong>{formatJpy(totalAmount)}</strong>
                </div>
                <PreviewRow label="Hình thức TT" empty={!form.payment_method}>
                    {form.payment_method || "—"}
                </PreviewRow>
            </div>

            <div className="order-form-preview__section">
                <h4 className="order-form-preview__section-title">Giao hàng</h4>
                <PreviewRow label="Vận chuyển" empty={!form.shipping_mode}>
                    {form.shipping_mode ? (
                        <span className={`crm-badge ${shippingModeBadgeClass(form.shipping_mode)}`}>
                            {shippingModeLabel(form.shipping_mode)}
                        </span>
                    ) : (
                        "—"
                    )}
                </PreviewRow>
                <PreviewRow label="Ngày giao" empty={!deliveryRange}>
                    {deliveryRange || "—"}
                </PreviewRow>
                <PreviewRow label="Khung giờ" empty={!form.delivery_time_slot.trim()}>
                    {form.delivery_time_slot.trim() || "—"}
                </PreviewRow>
            </div>

            {form.notes.trim() && (
                <div className="order-form-preview__section">
                    <h4 className="order-form-preview__section-title">Ghi chú</h4>
                    <p className="order-form-preview__note">{form.notes.trim()}</p>
                </div>
            )}

            {!customer && (
                <p className="order-form-preview__hint">Chọn khách hàng để bắt đầu xem trước đơn hàng.</p>
            )}
        </aside>
    );
}

export default function OrderFormPage() {
    const navigate = useNavigate();
    const [customers, setCustomers] = useState([]);
    const [pins, setPins] = useState([]);
    const [form, setForm] = useState(emptyForm);
    const [loading, setLoading] = useState(true);
    const [saving, setSaving] = useState(false);
    const [error, setError] = useState("");

    useEffect(() => {
        Promise.all([
            api.get("/customers", { params: { per_page: 100 } }),
            api.get("/pins", { params: { active_only: 1, per_page: 200 } }),
        ])
            .then(([customersResponse, pinsResponse]) => {
                setCustomers(customersResponse.data?.data ?? customersResponse.data ?? []);
                setPins(pinsResponse.data?.data ?? pinsResponse.data ?? []);
            })
            .catch(() => setError("Không tải được dữ liệu tạo đơn."))
            .finally(() => setLoading(false));
    }, []);

    const totalAmount = useMemo(() => {
        return Number(form.bike_price_jpy || 0) + Number(form.shipping_fee_jpy || 0);
    }, [form.bike_price_jpy, form.shipping_fee_jpy]);

    const selectedCustomer = useMemo(
        () => customers.find((customer) => String(customer.id) === String(form.customer_id)),
        [customers, form.customer_id]
    );

    const selectedPin = useMemo(
        () => pins.find((pin) => String(pin.id) === String(form.pin_id)),
        [pins, form.pin_id]
    );

    const handleChange = (event) => {
        const { name, value } = event.target;
        setForm((current) => ({ ...current, [name]: value }));
    };

    const handleCustomerChange = (event) => {
        setForm((current) => ({ ...current, customer_id: event.target.value }));
    };

    const handleSubmit = async (event) => {
        event.preventDefault();
        setSaving(true);
        setError("");

        if (
            form.delivery_date_from &&
            form.delivery_date_to &&
            form.delivery_date_to < form.delivery_date_from
        ) {
            setError("Ngày giao đến phải sau hoặc bằng ngày giao từ.");
            setSaving(false);
            return;
        }

        try {
            const response = await api.post("/orders", {
                customer_id: Number(form.customer_id),
                product_name: form.product_name.trim(),
                battery_capacity: selectedPin?.name || undefined,
                bike_price_jpy: Number(form.bike_price_jpy || 0),
                shipping_fee_jpy: Number(form.shipping_fee_jpy || 0),
                payment_method: form.payment_method || undefined,
                shipping_mode: form.shipping_mode,
                delivery_date_from: form.delivery_date_from || undefined,
                delivery_date_to: form.delivery_date_to || undefined,
                delivery_time_slot: form.delivery_time_slot || undefined,
                notes: form.notes || undefined,
            });
            navigate(`/orders/${response.data.id}`);
        } catch (requestError) {
            const message =
                requestError.response?.data?.message ||
                Object.values(requestError.response?.data?.errors || {})?.[0]?.[0] ||
                "Không tạo được đơn hàng.";
            setError(message);
        } finally {
            setSaving(false);
        }
    };

    if (loading) {
        return <p className="text-sm text-slate-500">Đang tải...</p>;
    }

    const customerSelected = Boolean(form.customer_id);

    return (
        <div className="order-form-layout">
            <section className="crm-card order-form-panel">
                <div className="mb-4 flex items-center justify-between">
                    <h2 className="text-lg font-semibold">Tạo đơn hàng mới</h2>
                    <Link to="/orders" className="text-sm text-blue-600 hover:underline">
                        Quay lại
                    </Link>
                </div>

                <form className="space-y-5" onSubmit={handleSubmit}>
                    <label className="crm-field">
                        <span>Khách hàng *</span>
                        <select name="customer_id" value={form.customer_id} onChange={handleCustomerChange} required>
                            <option value="">Chọn khách hàng</option>
                            {customers.map((customer) => (
                                <option key={customer.id} value={customer.id}>
                                    {customer.full_name} ({customer.phone})
                                </option>
                            ))}
                        </select>
                    </label>

                    {!customerSelected && (
                        <p className="rounded-lg border border-dashed border-slate-200 bg-slate-50 p-4 text-sm text-slate-500">
                            Chọn khách hàng trước, sau đó điền thông tin đơn bên dưới.
                        </p>
                    )}

                    {customerSelected && (
                        <div className="grid gap-4 md:grid-cols-2">
                            <label className="crm-field md:col-span-2">
                                <span>Sản phẩm *</span>
                                <input
                                    name="product_name"
                                    value={form.product_name}
                                    onChange={handleChange}
                                    placeholder="Yamaha / Panasonic, đời xe..."
                                    required
                                />
                            </label>

                            <label className="crm-field">
                                <span>Pin</span>
                                <select name="pin_id" value={form.pin_id} onChange={handleChange}>
                                    <option value="">-- Chọn pin --</option>
                                    {pins.map((pin) => (
                                        <option key={pin.id} value={pin.id}>
                                            {pin.bike_line ? `${pin.bike_line} — ${pin.name}` : pin.name}
                                        </option>
                                    ))}
                                </select>
                                {selectedPin?.description && (
                                    <p className="mt-1 text-xs text-slate-500">{selectedPin.description}</p>
                                )}
                                {pins.length === 0 && (
                                    <p className="mt-1 text-xs text-amber-700">Chưa có pin nào. Liên hệ admin để thêm pin.</p>
                                )}
                            </label>

                            <label className="crm-field">
                                <span>Giá xe (円) *</span>
                                <input
                                    type="number"
                                    min="0"
                                    name="bike_price_jpy"
                                    value={form.bike_price_jpy}
                                    onChange={handleChange}
                                    required
                                />
                            </label>

                            <label className="crm-field">
                                <span>Phí ship (円)</span>
                                <input
                                    type="number"
                                    min="0"
                                    name="shipping_fee_jpy"
                                    value={form.shipping_fee_jpy}
                                    onChange={handleChange}
                                />
                            </label>

                            <label className="crm-field">
                                <span>Tổng thanh toán</span>
                                <input
                                    type="text"
                                    value={formatJpy(totalAmount)}
                                    readOnly
                                    className="bg-slate-50 font-semibold"
                                />
                            </label>

                            <div className="crm-field md:col-span-2">
                                <span>Hình thức vận chuyển *</span>
                                <div className="crm-shipping-mode-grid">
                                    {SHIPPING_MODES.map((mode) => (
                                        <label
                                            key={mode.value}
                                            className={`crm-shipping-mode-option ${
                                                form.shipping_mode === mode.value ? "is-selected" : ""
                                            }`}
                                        >
                                            <input
                                                type="radio"
                                                name="shipping_mode"
                                                value={mode.value}
                                                checked={form.shipping_mode === mode.value}
                                                onChange={handleChange}
                                                required
                                            />
                                            <span className="crm-shipping-mode-title">{mode.label}</span>
                                        </label>
                                    ))}
                                </div>
                            </div>

                            <label className="crm-field md:col-span-2">
                                <span>Hình thức thanh toán</span>
                                <select name="payment_method" value={form.payment_method} onChange={handleChange}>
                                    {PAYMENT_METHODS.map((method) => (
                                        <option key={method} value={method}>
                                            {method}
                                        </option>
                                    ))}
                                </select>
                            </label>

                            <label className="crm-field">
                                <span>Giao từ ngày</span>
                                <input
                                    type="date"
                                    name="delivery_date_from"
                                    value={form.delivery_date_from}
                                    onChange={handleChange}
                                />
                            </label>

                            <label className="crm-field">
                                <span>Giao đến ngày</span>
                                <input
                                    type="date"
                                    name="delivery_date_to"
                                    value={form.delivery_date_to}
                                    min={form.delivery_date_from || undefined}
                                    onChange={handleChange}
                                />
                            </label>

                            <label className="crm-field md:col-span-2">
                                <span>Khung giờ giao</span>
                                <input
                                    name="delivery_time_slot"
                                    value={form.delivery_time_slot}
                                    onChange={handleChange}
                                    placeholder="VD: Sáng 8h-12h"
                                />
                            </label>

                            <label className="crm-field md:col-span-2">
                                <span>Ghi chú</span>
                                <textarea rows={4} name="notes" value={form.notes} onChange={handleChange} />
                            </label>
                        </div>
                    )}

                    {error && <p className="text-sm text-red-600">{error}</p>}

                    <div className="flex gap-2">
                        <button type="submit" className="crm-btn-primary" disabled={saving || !customerSelected}>
                            {saving ? "Đang tạo..." : "Tạo đơn hàng"}
                        </button>
                        <Link to="/orders" className="crm-btn-secondary">
                            Hủy
                        </Link>
                    </div>
                </form>
            </section>

            <OrderPreview
                form={form}
                customer={selectedCustomer}
                selectedPin={selectedPin}
                totalAmount={totalAmount}
            />
        </div>
    );
}
