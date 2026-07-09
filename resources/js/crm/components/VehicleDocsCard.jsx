import React from "react";

const VEHICLE_DOC_FIELDS = [
    { key: "chassis", label: "Số khung xe", getValue: (order) => order.vehicle_chassis_number },
    { key: "name_vi", label: "Họ tên tiếng Việt", getValue: (order) => order.vehicle_owner_name_vi },
    { key: "name_ja", label: "Họ tên tiếng Nhật", getValue: (order) => order.vehicle_owner_name_ja },
    { key: "postal", label: "Mã bưu điện", getValue: (order) => order.vehicle_owner_postal_code },
    { key: "phone", label: "SĐT", getValue: (order) => order.vehicle_owner_phone },
    { key: "address", label: "Địa chỉ", getValue: (order) => order.vehicle_owner_address, multiline: true },
];

function hasText(value) {
    return Boolean(value?.trim());
}

export default function VehicleDocsCard({ order, canEdit, onEdit }) {
    const filledFields = VEHICLE_DOC_FIELDS.filter((field) => hasText(field.getValue(order)));

    return (
        <div className="vehicle-docs-card">
            <div className="vehicle-docs-card__shell">
                <div className="vehicle-docs-card__glow vehicle-docs-card__glow--primary" aria-hidden="true" />
                <div className="vehicle-docs-card__glow vehicle-docs-card__glow--secondary" aria-hidden="true" />

                <div className="vehicle-docs-card__header">
                    <div className="vehicle-docs-card__heading">
                        <span className="vehicle-docs-card__icon" aria-hidden="true">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8">
                                <path
                                    d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"
                                    strokeLinecap="round"
                                />
                                <rect x="9" y="3" width="6" height="4" rx="1" />
                                <path d="M9 12h6M9 16h4" strokeLinecap="round" />
                            </svg>
                        </span>
                        <h4 className="vehicle-docs-card__title">Giấy tờ xe</h4>
                    </div>
                    {canEdit && (
                        <button type="button" className="vehicle-docs-card__edit-btn" onClick={onEdit}>
                            Cập nhật
                        </button>
                    )}
                </div>

                {filledFields.length > 0 ? (
                    <dl className="vehicle-docs-card__fields">
                        {filledFields.map((field) => (
                            <div
                                key={field.key}
                                className={`vehicle-docs-card__field${field.multiline ? " vehicle-docs-card__field--wide" : ""}`}
                            >
                                <dt className="vehicle-docs-card__label">{field.label}</dt>
                                <dd className={`vehicle-docs-card__value${field.multiline ? " vehicle-docs-card__value--multiline" : ""}`}>
                                    {field.getValue(order).trim()}
                                </dd>
                            </div>
                        ))}
                    </dl>
                ) : (
                    <p className="vehicle-docs-card__empty">
                        {canEdit ? "Chưa cập nhật giấy tờ xe." : "CTV chưa cập nhật giấy tờ xe."}
                    </p>
                )}
            </div>
        </div>
    );
}
