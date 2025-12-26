<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BalanceIssue;

class BalanceIssueController extends Controller
{
    public function index()
    {
        $issues = BalanceIssue::with('user', 'balanceHistory')->latest()->paginate(20);
        return view('admin.balance_issues.index', compact('issues'));
    }
    public  function delete_all()
    {
        BalanceIssue::truncate();
        return back()->with('success', '✅ Đã xoá tất cả balance issues!');
    }
}
