<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminPageController extends Controller
{
    public function __invoke(Request $request)
    {
        abort_unless($request->user()?->hasCrmRole('admin'), 403, 'Chỉ quản trị viên mới truy cập được trang này.');

        return view('admin.index');
    }
}
