<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAppearanceSetting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminAppearanceController extends Controller
{
    public function show(): JsonResponse
    {
        return response()->json(AdminAppearanceSetting::current()->toThemeArray());
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'background_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sidebar_background_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sidebar_text_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'sidebar_active_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
            'header_background_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        ]);

        $settings = AdminAppearanceSetting::current();
        $settings->fill($validated);
        $settings->save();

        return response()->json($settings->toThemeArray());
    }
}
