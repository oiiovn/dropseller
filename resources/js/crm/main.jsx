import React from "react";
import { createRoot } from "react-dom/client";
import { BrowserRouter } from "react-router";
import App from "./App";
import { AuthProvider } from "./context/AuthContext";
import { ThemeProvider } from "../shared/context/ThemeContext";
import { api } from "./lib/api";
import "../../css/crm.css";
import "../../css/tables-shared.css";
import "../../css/dashboard-filter.css";

createRoot(document.getElementById("crm-root")).render(
    <React.StrictMode>
        <BrowserRouter basename="/crm">
            <AuthProvider>
                <ThemeProvider readApi={api}>
                    <App />
                </ThemeProvider>
            </AuthProvider>
        </BrowserRouter>
    </React.StrictMode>
);
