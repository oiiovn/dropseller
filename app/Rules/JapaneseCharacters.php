<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class JapaneseCharacters implements ValidationRule
{
    private const PATTERN = '/^[\x{3040}-\x{309F}\x{30A0}-\x{30FF}\x{FF65}-\x{FF9F}\x{4E00}-\x{9FFF}\x{3400}-\x{4DBF}\x{3000}\s\x{30FC}\x{30FB}\x{3001}\x{3002}\x{3005}\x{3006}]+$/u';

    public function __construct(private readonly string $message = 'Chỉ được nhập ký tự tiếng Nhật (Hiragana, Katakana, Kanji).')
    {
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if ($value === null || $value === '') {
            return;
        }

        if (! preg_match(self::PATTERN, (string) $value)) {
            $fail($this->message);
        }
    }
}
