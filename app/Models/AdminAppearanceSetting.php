<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminAppearanceSetting extends Model
{
    public const DEFAULTS = [
        'background_color' => '#f4f4f5',
        'sidebar_background_color' => '#020617',
        'sidebar_text_color' => '#f4f4f5',
        'sidebar_active_color' => '#2563eb',
        'header_background_color' => '#ffffff',
    ];

    protected $fillable = [
        'background_color',
        'sidebar_background_color',
        'sidebar_text_color',
        'sidebar_active_color',
        'header_background_color',
    ];

    public static function current(): self
    {
        return static::query()->firstOrCreate([], self::DEFAULTS);
    }

    public function toThemeArray(): array
    {
        return [
            'background_color' => $this->background_color,
            'sidebar_background_color' => $this->sidebar_background_color,
            'sidebar_text_color' => $this->sidebar_text_color,
            'sidebar_active_color' => $this->sidebar_active_color,
            'header_background_color' => $this->header_background_color,
        ];
    }
}
