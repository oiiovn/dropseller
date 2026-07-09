import React, { useEffect, useRef } from "react";

export default function Dropdown({ isOpen, onClose, className = "", children }) {
    const ref = useRef(null);

    useEffect(() => {
        if (!isOpen) {
            return undefined;
        }

        const handleClick = (event) => {
            if (ref.current && !ref.current.contains(event.target)) {
                onClose();
            }
        };

        const handleEscape = (event) => {
            if (event.key === "Escape") {
                onClose();
            }
        };

        document.addEventListener("mousedown", handleClick);
        document.addEventListener("keydown", handleEscape);

        return () => {
            document.removeEventListener("mousedown", handleClick);
            document.removeEventListener("keydown", handleEscape);
        };
    }, [isOpen, onClose]);

    if (!isOpen) {
        return null;
    }

    return (
        <div ref={ref} className={className}>
            {children}
        </div>
    );
}
