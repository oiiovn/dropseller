import axios from "axios";

export const readCsrf = () =>
    document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

export const api = axios.create({
    baseURL: "/crm-api",
    headers: {
        "X-Requested-With": "XMLHttpRequest",
        Accept: "application/json",
    },
});

api.interceptors.request.use((config) => {
    const token = readCsrf();
    if (token && ["post", "put", "patch", "delete"].includes((config.method || "").toLowerCase())) {
        config.headers["X-CSRF-TOKEN"] = token;
    }
    return config;
});

export const formatJpy = (value) =>
    `${Number(value || 0).toLocaleString("ja-JP", { maximumFractionDigits: 0 })}円`;
