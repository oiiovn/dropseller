/** Ký tự tiếng Nhật: Hiragana, Katakana, Kanji, khoảng trắng, ー, ・ */
export const JAPANESE_NAME_PATTERN =
    /^[\u3040-\u309F\u30A0-\u30FF\uFF65-\uFF9F\u4E00-\u9FFF\u3400-\u4DBF\u3000\u0020\u30FC\u30FB\u3001\u3002\u3005\u3006\u30F0-\u30FF]*$/;

const NON_JAPANESE_CHARS =
    /[^\u3040-\u309F\u30A0-\u30FF\uFF65-\uFF9F\u4E00-\u9FFF\u3400-\u4DBF\u3000\u0020\u30FC\u30FB\u3001\u3002\u3005\u3006\u30F0-\u30FF]/g;

export function filterJapaneseName(value) {
    return String(value || "").replace(NON_JAPANESE_CHARS, "");
}

export function isValidJapaneseName(value) {
    const trimmed = String(value || "").trim();
    if (!trimmed) return true;
    return JAPANESE_NAME_PATTERN.test(trimmed);
}

export const JAPANESE_NAME_HINT = "Chỉ nhập Hiragana, Katakana hoặc Kanji";
