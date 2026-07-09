import React from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router";
import App from "./App";
import { AuthProvider } from "./context/AuthContext";
import { ThemeProvider } from "../shared/context/ThemeContext";
import { adminApi } from "./lib/api";
import "../../css/admin.css";
import "../../css/payments-shared.css";
import "../../css/tables-shared.css";
import "../../css/dashboard-filter.css";

createRoot(document.getElementById("admin-root")).render(
    <React.StrictMode>
        <BrowserRouter basename="/admin">
            <AuthProvider>
                <ThemeProvider readApi={adminApi} writeApi={adminApi}>
                    <App />
                </ThemeProvider>
            </AuthProvider>
        </BrowserRouter>
    </React.StrictMode>
);
