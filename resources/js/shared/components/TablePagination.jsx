import React from "react";

export default function TablePagination({
    currentPage = 1,
    lastPage = 1,
    total = 0,
    loading = false,
    onPageChange,
    buttonClassName = "crm-btn-sm",
}) {
    if (loading) {
        return null;
    }

    const hasData = total > 0 || lastPage > 0;

    if (!hasData) {
        return null;
    }

    return (
        <div className="table-pagination">
            <span className="table-pagination__info">
                Trang {currentPage}/{Math.max(lastPage, 1)}
                {total > 0 ? ` · ${total} bản ghi` : ""}
            </span>
            <div className="table-pagination__actions">
                <button
                    type="button"
                    className={buttonClassName}
                    disabled={currentPage <= 1}
                    onClick={() => onPageChange(currentPage - 1)}
                >
                    Trước
                </button>
                <button
                    type="button"
                    className={buttonClassName}
                    disabled={currentPage >= lastPage}
                    onClick={() => onPageChange(currentPage + 1)}
                >
                    Sau
                </button>
            </div>
        </div>
    );
}
