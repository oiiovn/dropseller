<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách người dùng
     */
    public function index()
    {
        $users = User::paginate(10);
        return view('admin.users.index', compact('users'));
    }

    /**
     * Hiển thị form tạo mới người dùng
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Lưu người dùng mới
     */
    public function store(Request $request)
    {
        // Xử lý tạo người dùng mới
        return redirect()->route('admin.users.index')->with('success', 'Tạo người dùng thành công');
    }

    /**
     * Hiển thị thông tin người dùng
     */
    public function show($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    /**
     * Hiển thị form chỉnh sửa người dùng
     */
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin người dùng
     */
    public function update(Request $request, $id)
    {
        // Xử lý cập nhật người dùng
        return redirect()->route('admin.users.index')->with('success', 'Cập nhật người dùng thành công');
    }

    /**
     * Xóa người dùng
     */
    public function destroy($id)
    {
        // Xử lý xóa người dùng
        return redirect()->route('admin.users.index')->with('success', 'Xóa người dùng thành công');
    }
}
