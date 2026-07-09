import React, { createContext, useCallback, useContext, useEffect, useMemo, useState } from "react";

export const DEFAULT_THEME = {
    background_color: "#f4f4f5",
    sidebar_background_color: "#020617",
    sidebar_text_color: "#f4f4f5",
    sidebar_active_color: "#2563eb",
    header_background_color: "#ffffff",
};

const ThemeContext = createContext(null);

export function themeToCssVars(theme) {
    return {
        "--app-bg": theme.background_color,
        "--app-sidebar-bg": theme.sidebar_background_color,
        "--app-sidebar-text": theme.sidebar_text_color,
        "--app-sidebar-active-bg": theme.sidebar_active_color,
        "--app-header-bg": theme.header_background_color,
    };
}

export function ThemeProvider({ children, readApi, writeApi = null }) {
    const [theme, setTheme] = useState(DEFAULT_THEME);
    const [loading, setLoading] = useState(true);

    useEffect(() => {
        readApi
            .get("/settings/appearance")
            .then((response) => setTheme({ ...DEFAULT_THEME, ...response.data }))
            .catch(() => setTheme(DEFAULT_THEME))
            .finally(() => setLoading(false));
    }, [readApi]);

    const updateTheme = useCallback(
        async (nextTheme) => {
            if (!writeApi) {
                throw new Error("Theme updates are only available in admin.");
            }

            const response = await writeApi.put("/settings/appearance", nextTheme);
            const saved = { ...DEFAULT_THEME, ...response.data };
            setTheme(saved);
            return saved;
        },
        [writeApi]
    );

    const cssVars = useMemo(() => themeToCssVars(theme), [theme]);

    const value = useMemo(
        () => ({
            theme,
            cssVars,
            loading,
            updateTheme,
            canUpdate: Boolean(writeApi),
        }),
        [theme, cssVars, loading, updateTheme, writeApi]
    );

    return <ThemeContext.Provider value={value}>{children}</ThemeContext.Provider>;
}

export function useTheme() {
    const context = useContext(ThemeContext);
    if (!context) {
        throw new Error("useTheme must be used within ThemeProvider");
    }
    return context;
}
