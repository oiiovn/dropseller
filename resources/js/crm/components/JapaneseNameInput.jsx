import React, { useState } from "react";
import { filterJapaneseName } from "../lib/japaneseInput";

export default function JapaneseNameInput({ value, onChange, className = "", showHint = false, hintClassName = "", ...props }) {
    const [composing, setComposing] = useState(false);

    const handleChange = (event) => {
        const nextValue = composing ? event.target.value : filterJapaneseName(event.target.value);
        onChange(nextValue);
    };

    const handleCompositionEnd = (event) => {
        setComposing(false);
        onChange(filterJapaneseName(event.target.value));
    };

    return (
        <>
            <input
                {...props}
                className={className}
                lang="ja"
                value={value}
                onChange={handleChange}
                onCompositionStart={() => setComposing(true)}
                onCompositionEnd={handleCompositionEnd}
            />
            {showHint && <p className={hintClassName || "mt-1 text-[14px] text-slate-400"}>Chỉ nhập ký tự tiếng Nhật</p>}
        </>
    );
}
