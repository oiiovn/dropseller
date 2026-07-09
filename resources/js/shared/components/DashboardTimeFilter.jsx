import React from "react";

function CalendarIcon() {
    return (
        <svg viewBox="0 0 20 20" fill="currentColor" className="h-4 w-4" aria-hidden="true">
            <path
                fillRule="evenodd"
                d="M5.75 2a.75.75 0 01.75.75V4h7V2.75a.75.75 0 011.5 0V4h.25A2.75 2.75 0 0118 6.75v8.5A2.75 2.75 0 0115.25 18H4.75A2.75 2.75 0 012 15.25v-8.5A2.75 2.75 0 014.75 4H5V2.75A.75.75 0 015.75 2zm-.25 4.5c-.69 0-1.25.56-1.25 1.25v6.5c0 .69.56 1.25 1.25 1.25h10.5c.69 0 1.25-.56 1.25-1.25v-6.5c0-.69-.56-1.25-1.25-1.25H5.5z"
                clipRule="evenodd"
            />
        </svg>
    );
}

export default function DashboardTimeFilter({
    month,
    dateFrom,
    dateTo,
    loading = false,
    filterLabel,
    onMonthChange,
    onDateFromChange,
    onDateToChange,
    onClear,
    clearButtonClassName = "dashboard-time-filter__clear",
}) {
    const hasFilter = Boolean(month || dateFrom || dateTo);

    return (
        <section className="dashboard-time-filter">
            <div className="dashboard-time-filter__row">
                <div className="dashboard-time-filter__intro">
                    <div className="dashboard-time-filter__icon">
                        <CalendarIcon />
                    </div>
                    <div className="dashboard-time-filter__heading">
                        <p className="dashboard-time-filter__title">Bộ lọc thời gian</p>
                        <p className="dashboard-time-filter__badge">
                            <span className="dashboard-time-filter__badge-dot" />
                            <span className="truncate">{filterLabel}</span>
                            {loading && <span className="dashboard-time-filter__loading">· Đang tải...</span>}
                        </p>
                    </div>
                </div>

                <div className="dashboard-time-filter__controls">
                    <div className="dashboard-time-filter__panel">
                        <label className="dashboard-time-filter__segment dashboard-time-filter__segment--month">
                            <span className="dashboard-time-filter__label">Theo tháng</span>
                            <input
                                type="month"
                                className="dashboard-time-filter__input"
                                value={month}
                                onChange={(event) => onMonthChange(event.target.value)}
                            />
                        </label>

                        <label className="dashboard-time-filter__segment">
                            <span className="dashboard-time-filter__label">Từ ngày</span>
                            <input
                                type="date"
                                className="dashboard-time-filter__input"
                                value={dateFrom}
                                max={dateTo || undefined}
                                onChange={(event) => onDateFromChange(event.target.value)}
                            />
                        </label>

                        <label className="dashboard-time-filter__segment">
                            <span className="dashboard-time-filter__label">Đến ngày</span>
                            <input
                                type="date"
                                className="dashboard-time-filter__input"
                                value={dateTo}
                                min={dateFrom || undefined}
                                onChange={(event) => onDateToChange(event.target.value)}
                            />
                        </label>
                    </div>

                    {hasFilter && (
                        <button type="button" className={clearButtonClassName} onClick={onClear}>
                            Xóa bộ lọc
                        </button>
                    )}
                </div>
            </div>
        </section>
    );
}
