<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /**
     * Hiển thị trang cài đặt hệ thống
     */
    public function index()
    {
        return view('admin.settings.index');
    }
}
