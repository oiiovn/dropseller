<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function viewProfile()
    {
        $user = Auth::user(); // Lấy thông tin người dùng hiện tại
        return view('profile.profile', compact('user'));
    }
}
