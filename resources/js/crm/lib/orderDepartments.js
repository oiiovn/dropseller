import { ORDER_STATUS_LABELS } from "./orderStatuses";

/** Bộ phận xử lý đơn — map sang trạng thái đích khi staff/CTV chuyển giao */
export const ORDER_DEPARTMENTS = [
    {
        value: "consulting",
        label: "Tư vấn",
        targetStatus: "consulting",
        fromStatuses: ["new"],
    },
    {
        value: "confirmed",
        label: "Chốt đơn",
        targetStatus: "confirmed",
        fromStatuses: ["new", "consulting"],
    },
    {
        value: "deposit_pending",
        label: "Chờ cọc",
        targetStatus: "deposit_pending",
        fromStatuses: ["confirmed"],
    },
    {
        value: "deposit_paid",
        label: "Ghi nhận cọc",
        targetStatus: "deposit_paid",
        fromStatuses: ["confirmed", "deposit_pending"],
    },
    {
        value: "packaging",
        label: "Đóng gói",
        targetStatus: "waiting_delivery",
        fromStatuses: ["confirmed", "deposit_pending", "deposit_paid"],
    },
    {
        value: "shipping",
        label: "Giao hàng",
        targetStatus: "delivering",
        fromStatuses: ["waiting_delivery", "packaged", "deposit_paid", "deposit_pending", "confirmed"],
    },
    {
        value: "delivery_confirm",
        label: "Xác nhận giao",
        targetStatus: "delivered",
        fromStatuses: ["delivering", "waiting_delivery", "packaged", "deposit_paid", "deposit_pending", "confirmed"],
    },
    {
        value: "complete",
        label: "Hoàn tất",
        targetStatus: "completed",
        fromStatuses: ["delivered", "delivering", "packaged", "waiting_delivery", "deposit_paid", "deposit_pending", "confirmed", "consulting"],
    },
    {
        value: "cancelled",
        label: "Hủy đơn",
        targetStatus: "cancelled",
        fromStatuses: ["new", "consulting", "confirmed", "deposit_pending", "deposit_paid", "waiting_delivery", "packaged", "delivering", "delivered"],
    },
    {
        value: "refunded",
        label: "Hoàn tiền",
        targetStatus: "refunded",
        fromStatuses: ["completed"],
    },
];

/** Bộ phận cho role Đóng gói vận chuyển */
export const PACKAGING_FULFILLMENT_DEPARTMENTS = [
    {
        value: "mark_packaged",
        label: "Đã đóng gói",
        targetStatus: "packaged",
        fromStatuses: ["waiting_delivery"],
    },
    {
        value: "shipping",
        label: "Đang giao",
        targetStatus: "delivering",
        fromStatuses: ["packaged"],
    },
    {
        value: "delivery_confirm",
        label: "Đã giao",
        targetStatus: "delivered",
        fromStatuses: ["delivering"],
    },
    {
        value: "cancelled",
        label: "Đã hủy",
        targetStatus: "cancelled",
        fromStatuses: ["waiting_delivery", "packaged", "delivering"],
    },
];

/** Thứ tự ưu tiên hiển thị bộ phận */
const DEPARTMENT_ORDER = [
    "packaging",
    "mark_packaged",
    "deposit_paid",
    "deposit_pending",
    "shipping",
    "delivery_confirm",
    "complete",
    "confirmed",
    "consulting",
    "cancelled",
    "refunded",
];

export function sortHandoffDepartments(departments) {
    return [...departments].sort((a, b) => {
        const aIndex = DEPARTMENT_ORDER.indexOf(a.value);
        const bIndex = DEPARTMENT_ORDER.indexOf(b.value);
        return (aIndex === -1 ? 99 : aIndex) - (bIndex === -1 ? 99 : bIndex);
    });
}

function filterDepartments(departments, orderStatus, allowedTransitions) {
    const allowed = new Set(allowedTransitions);

    return sortHandoffDepartments(
        departments.filter(
            (department) => department.fromStatuses.includes(orderStatus) && allowed.has(department.targetStatus)
        )
    );
}

/** Luôn trả danh sách bộ phận khi có quyền chuyển tiếp */
export function handoffDepartments(orderStatus, allowedTransitions = [], options = {}) {
    const { packagingRole = false } = options;

    if (packagingRole) {
        const packagingDepartments = filterDepartments(
            PACKAGING_FULFILLMENT_DEPARTMENTS,
            orderStatus,
            allowedTransitions
        );
        if (packagingDepartments.length > 0) {
            return packagingDepartments;
        }
    }

    const named = filterDepartments(ORDER_DEPARTMENTS, orderStatus, allowedTransitions);
    if (named.length > 0) {
        return named;
    }

    return allowedTransitions.map((status) => ({
        value: `status_${status}`,
        label: ORDER_STATUS_LABELS[status] || status,
        targetStatus: status,
        fromStatuses: [orderStatus],
    }));
}

export function defaultHandoffDepartment(departments) {
    return (
        departments.find((department) => department.value === "packaging") ||
        departments.find((department) => department.value === "mark_packaged") ||
        departments[0] ||
        null
    );
}

export function departmentLabel(value) {
    const all = [...ORDER_DEPARTMENTS, ...PACKAGING_FULFILLMENT_DEPARTMENTS];
    return all.find((department) => department.value === value)?.label || value;
}
